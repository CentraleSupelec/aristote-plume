from fastapi import FastAPI

from src.model.article_creation_task_dto import ArticleCreationTaskDto
from src.model.article_request import ArticleRequest
from src.worker import generate_article

fastapi_app = FastAPI()


@fastapi_app.post("/generate-article", response_model=ArticleCreationTaskDto)
def start_task(article_request: ArticleRequest) -> ArticleCreationTaskDto:
    generation_task = generate_article.delay(article_request.model_dump())

    return ArticleCreationTaskDto(id=generation_task.id)
