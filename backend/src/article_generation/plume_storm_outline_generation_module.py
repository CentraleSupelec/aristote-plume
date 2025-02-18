from typing import Union
from knowledge_storm import StormOutlineGenerationModule
import dspy

from src.article_generation.plume_write_outline import PlumeWriteOutline


class PlumeStormOutlineGenerationModule(
    StormOutlineGenerationModule
):  # pylint: disable=too-few-public-methods
    def __init__(
        self, outline_gen_lm: Union[dspy.dsp.LM, dspy.dsp.HFModel], language: str
    ):
        super().__init__(outline_gen_lm)
        self.write_outline = PlumeWriteOutline(engine=outline_gen_lm, language=language)
