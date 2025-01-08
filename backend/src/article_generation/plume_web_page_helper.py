import concurrent.futures
from typing import List, Dict, Union

import fitz
import requests
from knowledge_storm import WebPageHelper
from trafilatura import extract


class PlumeWebPageHelper(WebPageHelper):
    """Helper class to process web pages.

    Acknowledgement: Part of the code is adapted from https://github.com/stanford-oval/WikiChat project.
    """

    def __init__(
        self,
        min_char_count: int = 150,
        snippet_chunk_size: int = 500,
        snippet_chunk_overlap: int = 100,
        max_thread_num: int = 10,
    ):
        super().__init__(min_char_count, snippet_chunk_size, max_thread_num)
        self.text_splitter._chunk_overlap = snippet_chunk_overlap

    def urls_to_articles(self, urls: List[str]) -> Dict:
        with concurrent.futures.ThreadPoolExecutor(
            max_workers=self.max_thread_num
        ) as executor:
            articles_list = list(executor.map(self.url_to_article, urls))

        articles = {}

        for article_text, u in zip(articles_list, urls):
            if article_text is not None and len(article_text) > self.min_char_count:
                articles[u] = {"text": article_text}

        return articles

    def _extract_pdf_text_from_url(self, pdf_url: str) -> str:
        try:
            response = requests.get(pdf_url, timeout=60)
            response.raise_for_status()

            with fitz.open(stream=response.content, filetype="pdf") as doc:
                text = ""
                for page in doc:
                    text += page.get_text()

            return text

        except requests.exceptions.RequestException as e:
            print(f"Erreur lors du téléchargement : {e}")
        except Exception as e:  # pylint: disable=broad-exception-caught
            print(f"Erreur lors de l'extraction du texte : {e}")

        return ""

    def url_to_article(self, u: str) -> Union[str, None]:
        if "arxiv.org/pdf" in u:
            article_text = self._extract_pdf_text_from_url(u)

            return article_text

        h = self.download_webpage(u)
        if h is not None:
            article_text = extract(
                h,
                include_tables=False,
                include_comments=False,
                output_format="txt",
            )

            return article_text

        return None
