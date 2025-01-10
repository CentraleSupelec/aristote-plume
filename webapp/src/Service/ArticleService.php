<?php

namespace App\Service;

use App\Entity\Article;
use Aws\S3\S3Client;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
use Psr\Log\LoggerInterface;

readonly class ArticleService
{
    private CommonMarkConverter $commonMarkConverter;

    public function __construct(
        private LoggerInterface $logger,
        private S3Client $s3Client,
        private EntityManagerInterface $entityManager,
        private string $bucketName,
        private string $uploadDirectory,
    ) {
        $this->commonMarkConverter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * @throws CommonMarkException|Exception
     */
    public function buildAndSaveArticleContent(Article $article): void
    {
        $content = $this->buildArticleContent($article);
        $article->setContent($content);
        $this->entityManager->flush();
    }

    /**
     * @throws CommonMarkException|Exception
     */
    private function buildArticleContent(Article $article): string
    {
        try {
            $articleRawContent = $this->getArticleFileContent($article);
            $htmlArticleContent = $this->commonMarkConverter->convert($articleRawContent);
            $sources = $this->getArticleSources($article);

            // Build sources paragraph
            $htmlSourceParagraph = '<h1>Sources</h1>';
            $sourcesIndices = [];
            foreach ($sources as $url => $data) {
                $sourcesIndices[$data['index']] = $url;
                $htmlSourceParagraph .= sprintf(
                    '<div><a href="%s" target="_blank">[%s] %s</a></div>', $url, $data['index'], $data['title']
                );
            }

            // Replace sources indication in text by links
            foreach ($sourcesIndices as $index => $link) {
                $search = "[$index]";
                $replace = sprintf('<a href="%s" target="_blank">[%s]</a>', $link, $index);
                $htmlArticleContent = str_replace($search, $replace, $htmlArticleContent);
            }

            // Add sources at the end of the article
            $htmlArticleContent .= $htmlSourceParagraph;

            // Update headers style
            $htmlArticleContent = str_replace('<h1>', '<h1 class="h4 text-secondary">', $htmlArticleContent);
            $htmlArticleContent = str_replace('<h2>', '<h2 class="h5">', $htmlArticleContent);
            $htmlArticleContent = str_replace('<h3>', '<h3 class="h6">', $htmlArticleContent);
            $htmlArticleContent = str_replace('<h4>', '<h4 class="h6">', $htmlArticleContent);
            $htmlArticleContent = str_replace('<h5>', '<h5 class="h6">', $htmlArticleContent);

            return str_replace('<h6>', '<h6 class="h6">', $htmlArticleContent);
        } catch (CommonMarkException|Exception $e) {
            $this->logger->error($e);

            throw $e;
        }
    }

    private function getArticleFileContent(Article $article): string
    {
        $result = $this->s3Client->getObject([
            'Bucket' => $this->bucketName,
            'Key' => sprintf('%s/%s/storm_gen_article_polished.txt', $this->uploadDirectory, $article->getGenerationTaskId()),
        ]);

        return (string) $result['Body'];
    }

    /**
     * @throws Exception
     */
    private function getArticleSources(Article $article): array
    {
        $result = $this->s3Client->getObject([
            'Bucket' => $this->bucketName,
            'Key' => sprintf('%s/%s/url_to_info.json', $this->uploadDirectory, $article->getGenerationTaskId()),
        ]);
        $json = (string) $result['Body'];

        $fullSourcesArray = json_decode($json, true);
        if (
            !array_key_exists('url_to_unified_index', $fullSourcesArray)
            || !array_key_exists('url_to_info', $fullSourcesArray)
        ) {
            throw new Exception('Invalid sources JSON file (key "url_to_unified_index" or key "url_to_info" not found).');
        }

        $articleSourcesMapping = $fullSourcesArray['url_to_unified_index'];
        asort($articleSourcesMapping);

        $orderedHttpsSourcesMapping = [];
        foreach ($articleSourcesMapping as $key => $value) {
            $httpsKey = str_replace('http://', 'https://', $key);
            $orderedHttpsSourcesMapping[$httpsKey] = [
                'index' => $value,
                'title' => $fullSourcesArray['url_to_info'][$key]['title'],
            ];
        }

        return $orderedHttpsSourcesMapping;
    }
}
