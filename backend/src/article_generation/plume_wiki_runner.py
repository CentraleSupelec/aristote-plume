import json
import logging
import os
from typing import List

from knowledge_storm import STORMWikiRunner, STORMWikiRunnerArguments, STORMWikiLMConfigs, FileIOHelper, makeStringRed
from knowledge_storm.storm_wiki.modules.callback import BaseCallbackHandler
from knowledge_storm.storm_wiki.modules.storm_dataclass import StormInformationTable, StormArticle

from config.settings import get_settings, Settings
from src.article_generation.s3_storage_service import S3StorageService

logger = logging.getLogger(__name__)
settings: Settings = get_settings()

class PlumeWikiRunner(STORMWikiRunner):
    def __init__(
        self, args: STORMWikiRunnerArguments, lm_configs: STORMWikiLMConfigs, rm
    ):
        super().__init__(args=args, lm_configs=lm_configs, rm=rm)

    def run(
        self,
        topic: str,
        ground_truth_url: str = "",
        do_research: bool = True,
        do_generate_outline: bool = True,
        do_generate_article: bool = True,
        do_polish_article: bool = True,
        remove_duplicate: bool = False,
        callback_handler: BaseCallbackHandler = BaseCallbackHandler(),
        celery_task_id: str = '',
    ):
        """
        Run the STORM pipeline.

        Args:
            topic: The topic to research.
            ground_truth_url: A ground truth URL including a curated article about the topic. The URL will be excluded.
            do_research: If True, research the topic through information-seeking conversation;
             if False, expect conversation_log.json and raw_search_results.json to exist in the output directory.
            do_generate_outline: If True, generate an outline for the topic;
             if False, expect storm_gen_outline.txt to exist in the output directory.
            do_generate_article: If True, generate a curated article for the topic;
             if False, expect storm_gen_article.txt to exist in the output directory.
            do_polish_article: If True, polish the article by adding a summarization section and (optionally) removing
             duplicated content.
            remove_duplicate: If True, remove duplicated content.
            callback_handler: A callback handler to handle the intermediate results.
            celery_task_id: The uuid of the celery task carrying the article generation
        """
        assert (
            do_research
            or do_generate_outline
            or do_generate_article
            or do_polish_article
        ), makeStringRed(
            "No action is specified. Please set at least one of --do-research, --do-generate-outline, --do-generate-article, --do-polish-article"
        )

        self.topic = topic
        self.article_dir_name = celery_task_id
        self.article_output_dir = os.path.join(self.args.output_dir, self.article_dir_name)
        os.makedirs(self.article_output_dir, exist_ok=True)

        # research module
        information_table: StormInformationTable = None
        if do_research:
            information_table = self.run_knowledge_curation_module(
                ground_truth_url=ground_truth_url, callback_handler=callback_handler
            )
        # outline generation module
        outline: StormArticle = None
        if do_generate_outline:
            # load information table if it's not initialized
            if information_table is None:
                information_table = self._load_information_table_from_local_fs(
                    os.path.join(self.article_output_dir, "conversation_log.json")
                )
            outline = self.run_outline_generation_module(
                information_table=information_table, callback_handler=callback_handler
            )

        # article generation module
        draft_article: StormArticle = None
        if do_generate_article:
            if information_table is None:
                information_table = self._load_information_table_from_local_fs(
                    os.path.join(self.article_output_dir, "conversation_log.json")
                )
            if outline is None:
                outline = self._load_outline_from_local_fs(
                    topic=topic,
                    outline_local_path=os.path.join(
                        self.article_output_dir, "storm_gen_outline.txt"
                    ),
                )
            draft_article = self.run_article_generation_module(
                outline=outline,
                information_table=information_table,
                callback_handler=callback_handler,
            )

        # article polishing module
        if do_polish_article:
            if draft_article is None:
                draft_article_path = os.path.join(
                    self.article_output_dir, "storm_gen_article.txt"
                )
                url_to_info_path = os.path.join(
                    self.article_output_dir, "url_to_info.json"
                )
                draft_article = self._load_draft_article_from_local_fs(
                    topic=topic,
                    draft_article_path=draft_article_path,
                    url_to_info_path=url_to_info_path,
                )
            self.run_article_polishing_module(
                draft_article=draft_article, remove_duplicate=remove_duplicate
            )

    def post_run(self):
        """
        Post-run operations, including:
        1. Dumping the run configuration.
        2. Dumping the LLM call history.
        3. Upload article working directory to s3
        4. Delete local cache directory if previous upload was successful
        """
        config_log = self.lm_configs.log()
        FileIOHelper.dump_json(
            config_log, os.path.join(self.article_output_dir, "run_config.json")
        )

        llm_call_history: List[dict] = self.lm_configs.collect_and_reset_lm_history()
        with open(
            os.path.join(self.article_output_dir, "llm_call_history.jsonl"), "w"
        ) as f:
            for call in llm_call_history:
                if "kwargs" in call:
                    call.pop(
                        "kwargs"
                    )  # All kwargs are dumped together to run_config.json.
                for key, value in call.items():
                    if not isinstance(value, dict):
                        if hasattr(value, 'to_dict'):
                            call[key]=value.to_dict()
                        elif hasattr(value, 'model_dump'):
                            call[key] = value.model_dump()
                        elif hasattr(value, 'dict'):
                            call[key] = value.dict()
                f.write(json.dumps(call) + "\n")

        s3_storage_service = S3StorageService(
            s3_storage_access_key=settings.s3_storage_access_key,
            s3_storage_secret_key=settings.s3_storage_secret_key,
            s3_storage_endpoint_url=settings.s3_storage_endpoint_url,
            s3_storage_bucket_name=settings.s3_storage_bucket_name,
            s3_storage_upload_directory=settings.s3_storage_upload_directory,
        )
        upload_error = s3_storage_service.upload_directory_to_s3(self.article_output_dir, self.article_dir_name)

        if not upload_error:
            try:
                S3StorageService.delete_local_directory_recursively(self.article_output_dir)
            except Exception as e:
                logger.error(f"Failed to delete cache directory {self.article_output_dir}: {e}")
