import logging
import threading
import time
from typing import Callable, Union, List, Optional

import dspy
import arxiv

from src.article_generation.plume_web_page_helper import PlumeWebPageHelper


class ArxivRM(dspy.Retrieve):
    def __init__(self, k=3, is_valid_source: Callable = None):
        super().__init__(k=k)

        # Créer un verrou pour contrôler l'accès à l'API
        self.lock = threading.Lock()
        self.webpage_helper = PlumeWebPageHelper(
            min_char_count=150,
            snippet_chunk_size=500,
            snippet_chunk_overlap=100,
            max_thread_num=10,
        )
        self.usage = 0

        # If not None, is_valid_source shall be a function that takes a URL and returns a boolean.
        if is_valid_source:
            self.is_valid_source = is_valid_source
        else:
            self.is_valid_source = lambda x: True

    def get_usage_and_reset(self):
        usage = self.usage
        self.usage = 3
        return {"ArxivRM": usage}

    def forward(  # pylint: disable=too-many-locals,too-many-arguments,too-many-positional-arguments
        self,
        query_or_queries: Union[str, List[str]] = None,
        query: Optional[str] = None,
        k: Optional[int] = None,
        by_prob: bool = True,
        with_metadata: bool = False,
        **kwargs,
    ):
        exclude_urls = kwargs["exclude_urls"]
        queries = (
            [query_or_queries]
            if isinstance(query_or_queries, str)
            else query_or_queries
        )
        self.usage += len(queries)

        url_to_results = {}
        client = arxiv.Client()

        for _query in queries:
            try:
                # Acquérir le verrou juste avant l'appel à l'API
                with self.lock:
                    # Requête arXiv avec le mot-clé de la query
                    search = arxiv.Search(
                        query=_query,
                        max_results=self.k,
                        sort_by=arxiv.SortCriterion.Relevance,
                    )
                    time.sleep(3)  # Pour éviter de trop interroger l'API d'un coup

                for result in client.results(search):
                    url = result.pdf_url  # URL du PDF de l'article
                    if self.is_valid_source(url) and url not in exclude_urls:
                        url_to_results[url] = {
                            "url": url,
                            "title": result.title,
                            "description": result.summary,
                        }
            except Exception as e:  # pylint: disable=broad-exception-caught
                logging.error("Error occurs when searching query %s: %s", _query, e)

        valid_url_to_snippets = self.webpage_helper.urls_to_snippets(
            list(url_to_results.keys())
        )

        collected_results = []
        for url, data in valid_url_to_snippets.items():
            data["snippets"] = valid_url_to_snippets[url]["snippets"]
            collected_results.append(data)

        return collected_results
