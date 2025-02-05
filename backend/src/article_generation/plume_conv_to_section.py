from knowledge_storm.storm_wiki.modules.article_generation import ConvToSection
import dspy
from src.article_generation.plume_write_section import PlumeWriteSection
from src.model.language import LANGUAGES


class PlumeConvToSection(ConvToSection):
    def __init__(self, *args, language: str, **kwargs):
        super().__init__(*args, **kwargs)
        language_params = LANGUAGES.get(language)

        PlumeWriteSection.__doc__ = PlumeWriteSection.__doc__.format(
            language=language_params.name, write_prompt=language_params.write_prompt
        )
        # pylint: disable=line-too-long
        PlumeWriteSection.output = dspy.OutputField(
            prefix=f"Write the section with proper inline citations (Start your writing with # section title. Don't include the page title or try to write other sections), generate the section in {language_params.name}:\n",
            format=str,
        )

        self.write_section = dspy.Predict(PlumeWriteSection)
