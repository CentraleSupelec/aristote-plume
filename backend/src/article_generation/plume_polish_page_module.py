from knowledge_storm.storm_wiki.modules.article_polish import PolishPageModule
import dspy
from src.article_generation.plume_polish_page import PlumePolishPage
from src.article_generation.plume_write_lead_section import PlumeWriteLeadSection

from src.model.language import LANGUAGES


class PlumePolishPageModule(PolishPageModule):
    def __init__(self, *args, language: str, **kwargs):
        super().__init__(*args, **kwargs)
        language_params = LANGUAGES.get(language)

        PlumeWriteLeadSection.__doc__ = PlumeWriteLeadSection.__doc__.format(
            language=language_params.name, write_prompt=language_params.write_prompt
        )
        PlumePolishPage.__doc__ = PlumePolishPage.__doc__.format(
            language=language_params.name, write_prompt=language_params.write_prompt
        )

        self.write_lead = dspy.Predict(PlumeWriteLeadSection)
        self.polish_page = dspy.Predict(PlumePolishPage)
