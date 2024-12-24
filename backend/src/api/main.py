from fastapi import FastAPI

from src.model.article_request import ArticleRequest
from src.worker import generate_article

fastapi_app = FastAPI()

@fastapi_app.post("/generate-article")
def start_task(article_request: ArticleRequest) -> dict:
    generation_task = generate_article.delay(article_request.model_dump())
    return {"id": generation_task.id}
