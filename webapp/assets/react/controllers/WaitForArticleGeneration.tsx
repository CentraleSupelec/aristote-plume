import React, {useEffect, useState} from 'react';
import Spinner from 'react-bootstrap/Spinner';
import ArticleDto from "../../interface/article-dto";
import {ArticleStatusDto} from "../../interface/article-status-dto";
import Routing from "fos-router";

interface WaitForArticleGenerationProps {
    serializedArticle: string;
}

const WaitForArticleGeneration = ({serializedArticle}: WaitForArticleGenerationProps) => {
    const [article, setArticle] = useState<ArticleDto>(null);
    const [articleStatus, setArticleStatus] = useState<ArticleStatusDto>(null);
    const [error, setError] = useState<boolean>(false);

    useEffect((): void => {
        const parsedArticle: ArticleDto = JSON.parse(serializedArticle);
        setArticle(parsedArticle);
    }, [serializedArticle])

    useEffect(() => {
        if (null === article) return;

        let isMounted = true;

        const fetchData = async () => {
            try {
                const response = await fetch(Routing.generate('article_check_status', {id: article.id})); // Replace with your API endpoint
                if (!response.ok) {
                    throw new Error(`Error: ${response.statusText}`);
                }
                const result: ArticleStatusDto = await response.json();
                console.log(result);
                if (isMounted) {
                    setArticleStatus(result);
                }
            } catch (err: any) {
                if (isMounted) {
                    console.error(err.message || 'Error while checking article status.');
                    setError(true);
                }
            }
        };

        fetchData();
        const interval = setInterval(fetchData, 3000);

        return () => {
            isMounted = false; // Cleanup flag
            clearInterval(interval); // Clear the interval when component unmounts
        };
    }, [article]);

    return (<div>
        {article !== null ?
            <>
                <div className="card">
                    <div className="card-body">
                        <p>Title : {article.requested_topic}</p>
                        <p>Type : {article.requested_type}</p>
                        <p>Language : {article.requested_language}</p>
                        <p>Model : {article.requested_language_model}</p>
                        <div className={'d-flex justify-content-center align-items-center my-3'}>
                            <Spinner animation="border"/>
                        </div>
                        {articleStatus !== null ?
                            <>
                                <p className={'text-center'}>Stage {articleStatus.stage_info.stage_number} / {articleStatus.stage_info.total_stages} </p>
                                <p className={'text-center'}>{articleStatus.stage_info.stage}</p>
                            </>
                            : <></>
                        }
                    </div>
                </div>
            </>
            : <>
                <div className="card">
                    <div className="card-body">
                        <div className={'d-flex justify-content-center align-items-center'}>
                        <Spinner animation="border"/>
                        </div>
                    </div>
                </div>
            </>
        }
    </div>);
}

export default WaitForArticleGeneration;
