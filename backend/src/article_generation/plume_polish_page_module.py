from knowledge_storm.storm_wiki.modules.article_polish import (
    PolishPage,
    PolishPageModule,
    WriteLeadSection,
)
import dspy
from src.model.class_docstrings import (
    WRITE_LEAD_SECTION_DOCSTRING,
    POLISH_PAGE_DOCSTRING,
)
from src.model.language import LANGUAGES


class PlumePolishPageModule(PolishPageModule):
    def __init__(self, *args, language: str, **kwargs):
        super().__init__(*args, **kwargs)
        language_params = LANGUAGES.get(language)

        WriteLeadSection.__doc__ = WRITE_LEAD_SECTION_DOCSTRING.format(
            language=language_params.name, write_prompt=language_params.write_prompt
        )
        PolishPage.__doc__ = POLISH_PAGE_DOCSTRING.format(
            language=language_params.name, write_prompt=language_params.write_prompt
        )

        self.write_lead = dspy.Predict(WriteLeadSection)
        self.polish_page = dspy.Predict(PolishPage)
