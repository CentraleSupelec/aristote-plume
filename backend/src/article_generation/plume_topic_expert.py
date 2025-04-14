from knowledge_storm import AnswerQuestion, TopicExpert, QuestionToQuery
import dspy
from src.model.class_docstrings import (
    QUESTION_TO_QUERY_DOCSTRING,
    ANSWER_QUESTION_DOCSTRING,
)
from src.model.language import LANGUAGES


class PlumeTopicExpert(TopicExpert):
    def __init__(self, *args, language: str, **kwargs):
        super().__init__(*args, **kwargs)
        language_params = LANGUAGES.get(language)

        QuestionToQuery.__doc__ = QUESTION_TO_QUERY_DOCSTRING.format(
            language=language_params.name, search_prompt=language_params.search_prompt
        )
        AnswerQuestion.__doc__ = ANSWER_QUESTION_DOCSTRING.format(
            language=language_params.name, respond_prompt=language_params.respond_prompt
        )

        self.generate_queries = dspy.Predict(QuestionToQuery)
        self.answer_question = dspy.Predict(AnswerQuestion)
