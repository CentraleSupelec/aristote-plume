from typing import Optional, Union
from knowledge_storm import (
    StormKnowledgeCurationModule,
    Retriever,
    StormPersonaGenerator,
)
import dspy
from src.article_generation.plume_conv_simulator import PlumeConvSimulator


class PlumeStormKnowledgeCurationModule(
    StormKnowledgeCurationModule
):  # pylint: disable=too-few-public-methods
    def __init__(  # pylint: disable=too-many-arguments,too-many-positional-arguments
        self,
        retriever: Retriever,
        persona_generator: Optional[StormPersonaGenerator],
        conv_simulator_lm: Union[dspy.dsp.LM, dspy.dsp.HFModel],
        question_asker_lm: Union[dspy.dsp.LM, dspy.dsp.HFModel],
        max_search_queries_per_turn: int,
        search_top_k: int,
        max_conv_turn: int,
        max_thread_num: int,
        language: str,
    ):
        super().__init__(
            retriever,
            persona_generator,
            conv_simulator_lm,
            question_asker_lm,
            max_search_queries_per_turn,
            search_top_k,
            max_conv_turn,
            max_thread_num,
        )
        self.conv_simulator = PlumeConvSimulator(
            topic_expert_engine=conv_simulator_lm,
            question_asker_engine=question_asker_lm,
            retriever=retriever,
            max_search_queries_per_turn=max_search_queries_per_turn,
            search_top_k=search_top_k,
            max_turn=max_conv_turn,
            language=language,
        )
