<?php

namespace App;

final class Constants
{
    public static function getArticleTypes(): array
    {
        return [
            'article.type.economics' => 'economics',
            'article.type.literature' => 'literature',
            'article.type.science' => 'science',
        ];
    }

    public static function getAvailableArticleTypes(): array
    {
        return ['economics', 'literature', 'science'];
    }

    public static function getArticleGenerationModels(): array
    {
        return [
            'article.generation_model.aristote' => 'aristote',
        ];
    }

    public static function getAvailableArticleGenerationModels(): array
    {
        return ['aristote'];
    }

    public static function getArticleLanguages(): array
    {
        return [
            'article.language.fr' => 'fr',
            'article.language.en' => 'en',
            'article.language.query' => 'query',
        ];
    }

    public static function getAvailableArticleLanguages(): array
    {
        return ['fr', 'en', 'query'];
    }
}
