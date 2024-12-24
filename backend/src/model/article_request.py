from pydantic import BaseModel

from src.model.language import Language


class ArticleRequest(BaseModel):
    topic: str
    language: str = Language.ENGLISH
    language_model: str = "casperhansen/llama-3-70b-instruct-awq"
