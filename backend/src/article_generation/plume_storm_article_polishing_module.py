from typing import Union
from knowledge_storm import StormArticlePolishingModule
import dspy
from src.article_generation.plume_polish_page_module import PlumePolishPageModule


class PlumeStormArticlePolishingModule(
    StormArticlePolishingModule
):  # pylint: disable=too-few-public-methods
    def __init__(
        self,
        article_gen_lm: Union[dspy.dsp.LM, dspy.dsp.HFModel],
        article_polish_lm: Union[dspy.dsp.LM, dspy.dsp.HFModel],
        language: str,
    ):
        super().__init__(article_gen_lm, article_polish_lm)
        self.polish_page = PlumePolishPageModule(
            write_lead_engine=article_gen_lm,
            polish_engine=article_polish_lm,
            language=language,
        )
