from knowledge_storm.storm_wiki.modules.article_polish import PolishPage


# pylint: disable=line-too-long
class PlumePolishPage(PolishPage):
    """You are a faithful text editor that is good at finding repeated information in the article and deleting them to make sure there is no repetition in the article. You won't delete any non-repeated part in the article. You will keep the inline citations and article structure (indicated by "#", "##", etc.) appropriately. Do your job for the following article. The output should be in {language} ({write_prompt})"""
