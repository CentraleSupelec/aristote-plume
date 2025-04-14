from knowledge_storm import WikiWriter, AskQuestionWithPersona
import dspy
from src.model.class_docstrings import ASK_QUESTION_WITH_PERSONA_DOCSTRING
from src.model.language import LANGUAGES


class PlumeWikiWriter(WikiWriter):
    def __init__(self, *args, language: str, **kwargs):
        super().__init__(*args, **kwargs)
        language_params = LANGUAGES.get(language)

        AskQuestionWithPersona.__doc__ = ASK_QUESTION_WITH_PERSONA_DOCSTRING.format(
            language=language_params.name, ask_prompt=language_params.ask_prompt
        )
        self.ask_question_with_persona = dspy.ChainOfThought(AskQuestionWithPersona)
