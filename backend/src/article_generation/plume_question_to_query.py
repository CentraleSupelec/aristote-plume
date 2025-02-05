from knowledge_storm import QuestionToQuery


# pylint: disable=line-too-long
class PlumeQuestionToQuery(QuestionToQuery):
    """You want to answer the question using Google search. What do you type in the search box? Please write the queries in {language} ({search_prompt}).
    Write the queries you will use in the following format:
    - query 1
    - query 2
    ...
    - query n"""
