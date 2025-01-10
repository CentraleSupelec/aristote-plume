import Routing from "fos-router";
import React, {useEffect, useState} from 'react';
import Spinner from 'react-bootstrap/Spinner';
import {
    trans,
    APP_ARTICLE_WAITING_PAGE_CARD_BODY_DESCRIPTION,
    APP_ARTICLE_WAITING_PAGE_CARD_BODY_TITLE,
    APP_ARTICLE_WAITING_PAGE_CARD_BODY_TYPE,
    APP_ARTICLE_WAITING_PAGE_CARD_BODY_LANGUAGE,
    APP_ARTICLE_WAITING_PAGE_CARD_BODY_LANGUAGE_MODEL,
    APP_ARTICLE_WAITING_PAGE_CARD_BODY_STAGE,
    APP_ARTICLE_WAITING_PAGE_CARD_BODY_ERROR,
    ARTICLE_STAGE_INITIALIZATION,
    ARTICLE_STAGE_KNOWLEDGE_CURATION,
    ARTICLE_STAGE_OUTLINE_GENERATION,
    ARTICLE_STAGE_ARTICLE_GENERATION,
    ARTICLE_STAGE_ARTICLE_POLISH,
    ARTICLE_STAGE_POST_RUN,
    ARTICLE_TYPE_ECONOMICS,
    ARTICLE_TYPE_LITERATURE,
    ARTICLE_TYPE_SCIENCE,
    ARTICLE_LANGUAGE_FR,
    ARTICLE_LANGUAGE_EN,
} from '../../translator';
import ArticleDto from "../../interface/article-dto";
import {ArticleStatusDto} from "../../interface/article-status-dto";
import {Message, NoParametersType} from "@symfony/ux-translator";

interface WaitForArticleGenerationProps {
    serializedArticle: string;
}

const WaitForArticleGeneration = ({serializedArticle}: WaitForArticleGenerationProps) => {
    const [article, setArticle] = useState<ArticleDto>(null);
    const [articleStatus, setArticleStatus] = useState<ArticleStatusDto>(null);
    const [error, setError] = useState<boolean>(false);

    const TASK_STATUS_PENDING: string = 'PENDING';
    const TASK_STATUS_STARTED: string = 'STARTED';
    const TASK_STATUS_PROGRESS: string = 'PROGRESS';

    useEffect((): void => {
        const parsedArticle: ArticleDto = JSON.parse(serializedArticle);
        setArticle(parsedArticle);
    }, [serializedArticle])

    useEffect(() => {
        if (null === article) return;

        let isMounted = true;

        const fetchData = async () => {
            const response = await fetch(Routing.generate('article_check_status', {id: article.id}));
            if (!response.ok) {
                throw new Error(`Error: ${response.statusText}`);
            }
            const result: ArticleStatusDto = await response.json();
            if (isMounted) {
                setArticleStatus(result);
                if (error) {
                    setError(false);
                }
            }
        };

        const handleError = (err: any): void => {
            if (isMounted) {
                console.error(err.message || 'Error while checking article status.');
                setError(true);
            }
        }

        fetchData().catch(handleError);
        const interval = setInterval(() => {
            fetchData().catch(handleError)
        }, 3000);

        return () => {
            isMounted = false;
            clearInterval(interval);
        };
    }, [article, error]);

    useEffect(() => {
        if (null === article || null === articleStatus) return;

        if (![TASK_STATUS_PENDING, TASK_STATUS_STARTED, TASK_STATUS_PROGRESS].includes(articleStatus.task_status)) {
            window.location.href = Routing.generate('article_detail_page', {id: article.id});
        }
    }, [article, articleStatus]);

    const getTranslatedArticleStageName = (key: string): Message<{
        'messages': { parameters: NoParametersType }
    }, string> => {
        switch (key) {
            case 'knowledge_curation':
                return ARTICLE_STAGE_KNOWLEDGE_CURATION;
            case 'outline_generation':
                return ARTICLE_STAGE_OUTLINE_GENERATION;
            case 'article_generation':
                return ARTICLE_STAGE_ARTICLE_GENERATION;
            case 'article_polish':
                return ARTICLE_STAGE_ARTICLE_POLISH;
            case 'post_run':
                return ARTICLE_STAGE_POST_RUN;
            default:
                return ARTICLE_STAGE_INITIALIZATION;
        }
    }

    const getTranslatedArticleLanguage = (key: string): Message<{
        'messages': { parameters: NoParametersType }
    }, string> => {
        switch (key) {
            case 'fr':
                return ARTICLE_LANGUAGE_FR;
            default:
                return ARTICLE_LANGUAGE_EN;
        }
    }

    const getTranslatedArticleType = (key: string): Message<{
        'messages': { parameters: NoParametersType }
    }, string> => {
        switch (key) {
            case 'economics':
                return ARTICLE_TYPE_ECONOMICS;
            case 'literature':
                return ARTICLE_TYPE_LITERATURE;
            default:
                return ARTICLE_TYPE_SCIENCE;
        }
    }

    return (<div>
        {article !== null ?
            <>
                <div className="card">
                    <div className="card-body">
                        <h2 className={'h4 mb-3 text-center text-secondary'}>
                            {trans(APP_ARTICLE_WAITING_PAGE_CARD_BODY_DESCRIPTION)}
                        </h2>
                        <p>
                            <span className={'text-secondary fw-bold'}>
                                {trans(APP_ARTICLE_WAITING_PAGE_CARD_BODY_TITLE)}
                            </span>
                            {article.requested_topic}
                        </p>
                        <p>
                            <span className={'text-secondary fw-bold'}>
                                {trans(APP_ARTICLE_WAITING_PAGE_CARD_BODY_TYPE)}
                            </span>
                            {trans(getTranslatedArticleType(article.requested_type))}
                        </p>
                        <p>
                            <span className={'text-secondary fw-bold'}>
                                {trans(APP_ARTICLE_WAITING_PAGE_CARD_BODY_LANGUAGE)}
                            </span>
                            {trans(getTranslatedArticleLanguage(article.requested_language))}
                        </p>
                        <p>
                            <span className={'text-secondary fw-bold'}>
                                {trans(APP_ARTICLE_WAITING_PAGE_CARD_BODY_LANGUAGE_MODEL)}
                            </span>
                            {article.requested_language_model}
                        </p>
                        <div className={'d-flex justify-content-center align-items-center my-3'}>
                            <Spinner animation="border" variant="primary" />
                        </div>
                        {articleStatus !== null && articleStatus.stage_info !== null ?
                            <>
                                <p className={'text-center text-primary fw-bold'}>
                                    {trans(APP_ARTICLE_WAITING_PAGE_CARD_BODY_STAGE)}
                                    {trans(getTranslatedArticleStageName(articleStatus.stage_info.stage))}
                                    &nbsp;({articleStatus.stage_info.stage_number} / {articleStatus.stage_info.total_stage_count})
                                </p>
                            </>
                            : <></>
                        }
                        {error ?
                            <>
                                <p className={'text-center text-danger fw-bold'}>
                                    {trans(APP_ARTICLE_WAITING_PAGE_CARD_BODY_ERROR)}
                                </p>
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
                            <Spinner animation="border" variant="primary" />
                        </div>
                    </div>
                </div>
            </>
        }
    </div>);
}

export default WaitForArticleGeneration;
