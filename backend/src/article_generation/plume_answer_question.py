from knowledge_storm import AnswerQuestion


# pylint: disable=line-too-long
class PlumeAnswerQuestion(AnswerQuestion):
    """You are an expert who can use information effectively. You are chatting with a Wikipedia writer who wants to write a Wikipedia page on topic you know. You have gathered the related information and will now use the information to form a response.
    Make your response as informative as possible, ensuring that every sentence is supported by the gathered information. If the [gathered information] is not directly related to the [topic] or [question], provide the most relevant answer based on the available information. If no appropriate answer can be formulated, respond with, “I cannot answer this question based on the available information,” and explain any limitations or gaps.
    Please provide the response in {language} ({respond_prompt}).
    """
