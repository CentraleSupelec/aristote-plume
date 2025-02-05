from knowledge_storm.storm_wiki.modules.outline_generation import (
    WritePageOutlineFromConv,
)


# pylint: disable=line-too-long
class PlumeWritePageOutlineFromConv(WritePageOutlineFromConv):
    """Improve an outline for a Wikipedia page. You already have a draft outline that covers the general information. Now you want to improve it based on the information learned from an information-seeking conversation to make it more informative. Generate the outline in {language} ({plan_prompt}).
    Here is the format of your writing:
    1. Use "#" Title" to indicate section title, "##" Title" to indicate subsection title, "###" Title" to indicate subsubsection title, and so on.
    2. Do not include other information.
    3. Do not include topic name itself in the outline.
    """
