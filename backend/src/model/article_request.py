from pydantic import BaseModel

from src.model.language import Language


class ArticleRequest(BaseModel):
    requested_topic: str
    requested_language: str = Language.ENGLISH
    requested_language_model: str = ""
