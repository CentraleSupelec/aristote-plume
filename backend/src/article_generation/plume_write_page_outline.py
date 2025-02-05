from knowledge_storm.storm_wiki.modules.outline_generation import WritePageOutline


# pylint: disable=line-too-long
class PlumeWritePageOutline(WritePageOutline):
    """Write an outline for a Wikipedia page. Generate the outline in {language} ({plan_prompt})
    Here is the format of your writing:
    1. Use "#" Title" to indicate section title, "##" Title" to indicate subsection title, "###" Title" to indicate subsubsection title, and so on.
    2. Do not include other information.
    3. Do not include topic name itself in the outline.
    """
