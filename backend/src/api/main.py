from fastapi import FastAPI
from celery.result import AsyncResult

from src.model.article_progress_status_dto import ArticleProgressStatusDto
from src.model.article_creation_task_dto import ArticleCreationTaskDto
from src.model.article_request import ArticleRequest
from src.worker import celery_app, generate_article

fastapi_app = FastAPI()


@fastapi_app.get("/health")
def get_app_health() -> str:
    return "OK"


@fastapi_app.post("/generate-article", response_model=ArticleCreationTaskDto)
def start_task(article_request: ArticleRequest) -> ArticleCreationTaskDto:
    generation_task = generate_article.delay(article_request.model_dump())

    return ArticleCreationTaskDto(id=generation_task.id)


@fastapi_app.get("/article-status/{task_id}", response_model=ArticleProgressStatusDto)
def get_task_status(task_id: str) -> ArticleProgressStatusDto:
    result = AsyncResult(task_id, app=celery_app)
    return ArticleProgressStatusDto(
        task_id=task_id, task_status=result.status, stage_info=result.info
    )
