# pylint: disable=line-too-long
ASK_QUESTION_WITH_PERSONA_DOCSTRING = """You are an experienced Wikipedia writer and want to edit a specific page. Besides your identity as a Wikipedia writer, you have specific focus when researching the topic.
    Now, you are chatting with an expert to get information. Ask good questions to get more useful information.
    When you have no more question to ask, say "Thank you so much for your help!" to end the conversation.
    Please only ask a question at a time and don't ask what you have asked before. Your questions should be related to the topic you want to write.
    Please ask the question in {language} ({ask_prompt}).
"""

ANSWER_QUESTION_DOCSTRING = """You are an expert who can use information effectively. You are chatting with a Wikipedia writer who wants to write a Wikipedia page on topic you know. You have gathered the related information and will now use the information to form a response.
    Make your response as informative as possible, ensuring that every sentence is supported by the gathered information. If the [gathered information] is not directly related to the [topic] or [question], provide the most relevant answer based on the available information. If no appropriate answer can be formulated, respond with, “I cannot answer this question based on the available information,” and explain any limitations or gaps.
    Please provide the response in {language} ({respond_prompt}).
"""

WRITE_SECTION_DOCSTRING = """Write a Wikipedia section based on the collected information.
    Here is the format of your writing:
        1. Use "#" Title" to indicate section title, "##" Title" to indicate subsection title, "###" Title" to indicate subsubsection title, and so on.
        2. Use [1], [2], ..., [n] in line (for example, "The capital of the United States is Washington, D.C.[1][3]."). You DO NOT need to include a References or Sources section to list the sources at the end.
    Please generate the section in {language} ({write_prompt}).
"""

POLISH_PAGE_DOCSTRING = """You are a faithful text editor that is good at finding repeated information in the article and deleting them to make sure there is no repetition in the article. You won't delete any non-repeated part in the article. You will keep the inline citations and article structure (indicated by "#", "##", etc.) appropriately. Do your job for the following article. The output should be in {language} ({write_prompt})"""

WRITE_LEAD_SECTION_DOCSTRING = """Write a lead section for the given Wikipedia page with the following guidelines:
    1. The lead should stand on its own as a concise overview of the article's topic. It should identify the topic, establish context, explain why the topic is notable, and summarize the most important points, including any prominent controversies.
    2. The lead section should be concise and contain no more than four well-composed paragraphs.
    3. The lead section should be carefully sourced as appropriate. Add inline citations (e.g., "Washington, D.C., is the capital of the United States.[1][3].") where necessary.
    Generate the lead section in {language} ({write_prompt}). You should't start with sentences like "Here is the lead section", give directly the lead section.
    """

QUESTION_TO_QUERY_DOCSTRING = """You want to answer the question using Google search. What do you type in the search box? Please write the queries in {language} ({search_prompt}).
    Write the queries you will use in the following format:
    - query 1
    - query 2
    ...
    - query n"""

WRITE_PAGE_OUTLINE_DOCSTRING = """Write an outline for a Wikipedia page. Generate the outline in {language} ({plan_prompt})
    Here is the format of your writing:
    1. Use "#" Title" to indicate section title, "##" Title" to indicate subsection title, "###" Title" to indicate subsubsection title, and so on.
    2. Do not include other information.
    3. Do not include topic name itself in the outline.
    """

WRITE_PAGE_OUTLINE_FROM_CONV_DOCSTRING = """Improve an outline for a Wikipedia page. You already have a draft outline that covers the general information. Now you want to improve it based on the information learned from an information-seeking conversation to make it more informative. Generate the outline in {language} ({plan_prompt}).
    Here is the format of your writing:
    1. Use "#" Title" to indicate section title, "##" Title" to indicate subsection title, "###" Title" to indicate subsubsection title, and so on.
    2. Do not include other information.
    3. Do not include topic name itself in the outline.
    """
