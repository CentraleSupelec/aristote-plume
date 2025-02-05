from knowledge_storm import WikiWriter
import dspy
from src.article_generation.plume_ask_question_with_persona import (
    PlumeAskQuestionWithPersona,
)

from src.model.language import LANGUAGES


class PlumeWikiWriter(WikiWriter):
    def __init__(self, *args, language: str, **kwargs):
        super().__init__(*args, **kwargs)
        language_params = LANGUAGES.get(language)

        PlumeAskQuestionWithPersona.__doc__ = (
            PlumeAskQuestionWithPersona.__doc__.format(
                language=language_params.name, ask_prompt=language_params.ask_prompt
            )
        )
        self.ask_question_with_persona = dspy.ChainOfThought(
            PlumeAskQuestionWithPersona
        )
