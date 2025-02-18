from knowledge_storm import StormArticleGenerationModule
from src.article_generation.plume_conv_to_section import PlumeConvToSection


class PlumeStormArticleGenerationModule(StormArticleGenerationModule):
    def __init__(self, *args, language: str, **kwargs):
        super().__init__(*args, **kwargs)
        self.section_gen = PlumeConvToSection(
            engine=self.article_gen_lm, language=language
        )
