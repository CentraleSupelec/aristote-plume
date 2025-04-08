from knowledge_storm.storm_wiki.modules.outline_generation import (
    WriteOutline,
    WritePageOutline,
    WritePageOutlineFromConv,
)
import dspy
from src.model.class_docstrings import (
    WRITE_PAGE_OUTLINE_DOCSTRING,
    WRITE_PAGE_OUTLINE_FROM_CONV_DOCSTRING,
)
from src.model.language import LANGUAGES


class PlumeWriteOutline(WriteOutline):
    def __init__(self, *args, language: str, **kwargs):
        super().__init__(*args, **kwargs)
        language_params = LANGUAGES.get(language)

        WritePageOutline.__doc__ = WRITE_PAGE_OUTLINE_DOCSTRING.format(
            language=language_params.name, plan_prompt=language_params.plan_prompt
        )
        WritePageOutlineFromConv.__doc__ = (
            WRITE_PAGE_OUTLINE_FROM_CONV_DOCSTRING.format(
                language=language_params.name, plan_prompt=language_params.plan_prompt
            )
        )

        self.draft_page_outline = dspy.Predict(WritePageOutline)
        self.write_page_outline = dspy.Predict(WritePageOutlineFromConv)
