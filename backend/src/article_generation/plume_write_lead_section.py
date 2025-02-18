from knowledge_storm.storm_wiki.modules.article_polish import WriteLeadSection


# pylint: disable=line-too-long
class PlumeWriteLeadSection(WriteLeadSection):
    """Write a lead section for the given Wikipedia page with the following guidelines:
    1. The lead should stand on its own as a concise overview of the article's topic. It should identify the topic, establish context, explain why the topic is notable, and summarize the most important points, including any prominent controversies.
    2. The lead section should be concise and contain no more than four well-composed paragraphs.
    3. The lead section should be carefully sourced as appropriate. Add inline citations (e.g., "Washington, D.C., is the capital of the United States.[1][3].") where necessary.
    Generate the lead section in {language} ({write_prompt}). You should't start with sentences like "Here is the lead section", give directly the lead section.
    """
