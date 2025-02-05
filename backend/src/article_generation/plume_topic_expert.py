from knowledge_storm import TopicExpert
import dspy
from src.article_generation.plume_answer_question import PlumeAnswerQuestion
from src.article_generation.plume_question_to_query import PlumeQuestionToQuery

from src.model.language import LANGUAGES


class PlumeTopicExpert(TopicExpert):
    def __init__(self, *args, language: str, **kwargs):
        super().__init__(*args, **kwargs)
        language_params = LANGUAGES.get(language)

        PlumeQuestionToQuery.__doc__ = PlumeQuestionToQuery.__doc__.format(
            language=language_params.name, search_prompt=language_params.search_prompt
        )
        PlumeAnswerQuestion.__doc__ = PlumeAnswerQuestion.__doc__.format(
            language=language_params.name, respond_prompt=language_params.respond_prompt
        )

        self.generate_queries = dspy.Predict(PlumeQuestionToQuery)
        self.answer_question = dspy.Predict(PlumeAnswerQuestion)
