from typing import Union
from knowledge_storm import ConvSimulator, Retriever
import dspy

from src.article_generation.plume_topic_expert import PlumeTopicExpert
from src.article_generation.plume_wiki_writer import PlumeWikiWriter


class PlumeConvSimulator(ConvSimulator):
    def __init__(  # pylint: disable=too-many-arguments,too-many-positional-arguments
        self,
        topic_expert_engine: Union[dspy.dsp.LM, dspy.dsp.HFModel],
        question_asker_engine: Union[dspy.dsp.LM, dspy.dsp.HFModel],
        retriever: Retriever,
        max_search_queries_per_turn: int,
        search_top_k: int,
        max_turn: int,
        language: str,
    ):
        super().__init__(
            topic_expert_engine,
            question_asker_engine,
            retriever,
            max_search_queries_per_turn,
            search_top_k,
            max_turn,
        )
        self.wiki_writer = PlumeWikiWriter(
            engine=question_asker_engine, language=language
        )
        self.topic_expert = PlumeTopicExpert(
            engine=topic_expert_engine,
            max_search_queries=max_search_queries_per_turn,
            search_top_k=search_top_k,
            retriever=retriever,
            language=language,
        )
