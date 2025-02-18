from knowledge_storm.storm_wiki.modules.outline_generation import WriteOutline
import dspy
from src.article_generation.plume_write_page_outline_from_conv import (
    PlumeWritePageOutlineFromConv,
)
from src.article_generation.plume_write_page_outline import PlumeWritePageOutline

from src.model.language import LANGUAGES


class PlumeWriteOutline(WriteOutline):
    def __init__(self, *args, language: str, **kwargs):
        super().__init__(*args, **kwargs)
        language_params = LANGUAGES.get(language)

        PlumeWritePageOutline.__doc__ = PlumeWritePageOutline.__doc__.format(
            language=language_params.name, plan_prompt=language_params.plan_prompt
        )
        PlumeWritePageOutlineFromConv.__doc__ = (
            PlumeWritePageOutlineFromConv.__doc__.format(
                language=language_params.name, plan_prompt=language_params.plan_prompt
            )
        )

        self.draft_page_outline = dspy.Predict(PlumeWritePageOutline)
        self.write_page_outline = dspy.Predict(PlumeWritePageOutlineFromConv)
