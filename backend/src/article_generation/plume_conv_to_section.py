from knowledge_storm.storm_wiki.modules.article_generation import (
    ConvToSection,
    WriteSection,
)
import dspy
from src.model.class_docstrings import WRITE_SECTION_DOCSTRING
from src.model.language import LANGUAGES


class PlumeConvToSection(ConvToSection):
    def __init__(self, *args, language: str, **kwargs):
        super().__init__(*args, **kwargs)
        language_params = LANGUAGES.get(language)

        WriteSection.__doc__ = WRITE_SECTION_DOCSTRING.format(
            language=language_params.name, write_prompt=language_params.write_prompt
        )
        # pylint: disable=line-too-long
        WriteSection.output = dspy.OutputField(
            prefix=f"Write the section with proper inline citations (Start your writing with # section title. Don't include the page title or try to write other sections), generate the section in {language_params.name}:\n",
            format=str,
        )

        self.write_section = dspy.Predict(WriteSection)
