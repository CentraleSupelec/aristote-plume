from pydantic import BaseModel


class ArticleCreationTaskDto(BaseModel):
    id: str
