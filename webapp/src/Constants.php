<?php

namespace App;

use App\Entity\Article;

final class Constants
{
    public static function getArticleTypes(): array
    {
        return [
            'article.type.science' => Article::ARTICLE_TYPE_SCIENCE,
            'article.type.economics' => Article::ARTICLE_TYPE_ECONOMICS,
            'article.type.literature' => Article::ARTICLE_TYPE_LITERATURE,
        ];
    }

    public static function getAvailableArticleTypes(): array
    {
        return [Article::ARTICLE_TYPE_SCIENCE, Article::ARTICLE_TYPE_ECONOMICS, Article::ARTICLE_TYPE_LITERATURE];
    }

    public static function getArticleGenerationModels(): array
    {
        return [
            'article.generation_model.aristote' => 'casperhansen/llama-3-70b-instruct-awq',
        ];
    }

    public static function getAvailableArticleGenerationModels(): array
    {
        return ['casperhansen/llama-3-70b-instruct-awq'];
    }

    public static function getArticleLanguages(): array
    {
        return [
            'article.language.en' => Article::ARTICLE_LANGUAGE_EN,
            'article.language.fr' => Article::ARTICLE_LANGUAGE_FR,
        ];
    }

    public static function getAvailableArticleLanguages(): array
    {
        return [Article::ARTICLE_LANGUAGE_EN, Article::ARTICLE_LANGUAGE_FR];
    }

    public static function getAvailableArticleProgressStages(): array
    {
        return [
            'initialization',
            'knowledge_curation',
            'outline_generation',
            'article_generation',
            'article_polish',
            'post_run',
        ];
    }

    public static function getAvailableArticleTaskStatuses(): array
    {
        return [
            Article::ARTICLE_GENERATION_TASK_STATUS_PENDING,
            Article::ARTICLE_GENERATION_TASK_STATUS_STARTED,
            Article::ARTICLE_GENERATION_TASK_STATUS_PROGRESS,
            Article::ARTICLE_GENERATION_TASK_STATUS_SUCCESS,
            Article::ARTICLE_GENERATION_TASK_STATUS_FAILURE,
            Article::ARTICLE_GENERATION_TASK_STATUS_RETRY,
            Article::ARTICLE_GENERATION_TASK_STATUS_REVOKED,
        ];
    }
}
