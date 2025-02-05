from datetime import datetime
from zoneinfo import ZoneInfo

from celery import Celery, current_task
from celery.utils.log import get_task_logger
from knowledge_storm import STORMWikiLMConfigs, VLLMClient, STORMWikiRunnerArguments

from config.settings import get_settings, Settings
from src.model.article_progress_stage import (
    ArticleProgressStage,
    TOTAL_PROGRESS_STAGES,
    get_stage_index,
)
from src.model.article_generation_task_status import ArticleGenerationTaskStatus
from src.model.article_request import ArticleRequest
from src.article_generation.arxiv_rm import ArxivRM
from src.article_generation.plume_wiki_runner import PlumeWikiRunner

logger = get_task_logger(__name__)

settings: Settings = get_settings()
celery_app = Celery(
    main="article_generator",
    broker=settings.redis_broker_dsn,
    backend=settings.redis_backend_dsn,
)

celery_app.conf.update(
    task_track_started=True,
    result_extended=True,
)


@celery_app.task
def generate_article(article_request_dict: dict) -> None:
    article_request = ArticleRequest.model_validate(article_request_dict)
    logger.info(f"Generating article on topic: {article_request.requested_topic}...")
    current_task.update_state(
        state=ArticleGenerationTaskStatus.PROGRESS.value,
        meta={
            "stage": ArticleProgressStage.INITIALIZATION.value,
            "total_stage_count": TOTAL_PROGRESS_STAGES,
            "stage_number": get_stage_index(ArticleProgressStage.INITIALIZATION),
            "stage_start_date": datetime.now(ZoneInfo("Europe/Paris")).strftime(
                "%Y-%m-%d %H:%M:%S"
            ),
        },
    )

    lm_configs = STORMWikiLMConfigs()

    llama3_500 = VLLMClient(
        model=article_request.requested_language_model,
        url=settings.aristote_dispatcher_uri,
        port=settings.aristote_dispatcher_port,
        api_key=settings.aristote_dispatcher_api_key,
        max_tokens=500,
    )
    llama3_700 = VLLMClient(
        model=article_request.requested_language_model,
        url=settings.aristote_dispatcher_uri,
        port=settings.aristote_dispatcher_port,
        api_key=settings.aristote_dispatcher_api_key,
        max_tokens=700,
    )
    llama3_4000 = VLLMClient(
        model=article_request.requested_language_model,
        url=settings.aristote_dispatcher_uri,
        port=settings.aristote_dispatcher_port,
        api_key=settings.aristote_dispatcher_api_key,
        max_tokens=4000,
    )
    lm_configs.set_conv_simulator_lm(llama3_500)
    lm_configs.set_question_asker_lm(llama3_500)
    lm_configs.set_outline_gen_lm(llama3_500)
    lm_configs.set_article_gen_lm(llama3_700)
    lm_configs.set_article_polish_lm(llama3_4000)

    # Check out the STORMWikiRunnerArguments class for more configurations.
    engine_args = STORMWikiRunnerArguments(
        output_dir="./results_cache",
        max_conv_turn=3,
        max_perspective=3,
        search_top_k=3,
        retrieve_top_k=3,
        max_search_queries_per_turn=3,
        max_thread_num=10,
    )
    rm = ArxivRM(k=engine_args.search_top_k)
    runner = PlumeWikiRunner(
        engine_args, lm_configs, rm, article_request.requested_language
    )
    runner.run(
        topic=article_request.requested_topic,
        do_research=True,
        do_generate_outline=True,
        do_generate_article=True,
        do_polish_article=True,
        celery_task=current_task,
    )
    current_task.update_state(
        state=ArticleGenerationTaskStatus.PROGRESS.value,
        meta={
            "stage": ArticleProgressStage.POST_RUN.value,
            "total_stage_count": TOTAL_PROGRESS_STAGES,
            "stage_number": get_stage_index(ArticleProgressStage.POST_RUN),
            "stage_start_date": datetime.now(ZoneInfo("Europe/Paris")).strftime(
                "%Y-%m-%d %H:%M:%S"
            ),
        },
    )
    runner.post_run()
    runner.summary()

    logger.info("Article has been successfully generated.")
