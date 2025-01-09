from enum import Enum

TOTAL_PROGRESS_STAGES = 5


class ArticleProgressStage(Enum):
    INITIALIZATION = "article.progress.initialization"
    KNOWLEDGE_CURATION = "article.progress.knowledge_curation"
    OUTLINE_GENERATION = "article.progress.outline_generation"
    ARTICLE_GENERATION = "article.progress.article_generation"
    ARTICLE_POLISH = "article.progress.article_polish"
    POST_RUN = "article.progress.post_run"


def get_stage_index(stage: ArticleProgressStage) -> int:
    if stage == ArticleProgressStage.KNOWLEDGE_CURATION:
        return 1
    if stage == ArticleProgressStage.OUTLINE_GENERATION:
        return 2
    if stage == ArticleProgressStage.ARTICLE_GENERATION:
        return 3
    if stage == ArticleProgressStage.ARTICLE_POLISH:
        return 4
    if stage == ArticleProgressStage.POST_RUN:
        return 5
    return 0
