from typing import Dict, Any, Union

from pydantic import BaseModel


class ArticleProgressStatusDto(BaseModel):
    task_id: str
    # use union with any type below to get around non-precise celery typing
    task_status: Union[str, Any]
    stage_info: Union[Dict, Any]
