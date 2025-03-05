<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\PlumeUser;
use App\Form\ArticleCreationType;
use App\Model\ArticleCreationTaskDto;
use App\Model\ArticleProgressStatusDto;
use App\Repository\ArticleRepository;
use App\Security\Voter\ArticleVoter;
use App\Service\ArticleService;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use League\CommonMark\Exception\CommonMarkException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[IsGranted(PlumeUser::ROLE_DEFAULT)]
class ArticleController extends AbstractController
{
    #[Route('/app', name: 'articles_list')]
    public function listArticles(
        ArticleRepository $articleRepository,
        PaginatorInterface $paginator,
        #[CurrentUser] PlumeUser $plumeUser,
        Request $request,
    ): Response {
        $pagination = $paginator->paginate(
            $articleRepository->getArticlesForUserQueryBuilder($plumeUser),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('articles_list.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/app/create', name: 'article_create')]
    public function createArticle(
        #[CurrentUser] PlumeUser $user,
        Request $request,
        EntityManagerInterface $entityManager,
        HttpClientInterface $fastApiClient,
        HttpClientInterface $aristoteClient,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        LoggerInterface $logger,
    ): Response {
        $article = (new Article())->setAuthor($user);

        $modelIds = [];
        try {
            $models = $aristoteClient->request('GET', 'v1/models')->toArray();
            foreach ($models as $model) {
                $modelIds[$model['id']] = $model['id'];
            }
        } catch (ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            $this->addFlash('danger', 'app_article_create.error.api_no_models');
            $logger->error($e);
        }

        $form = $this->createForm(ArticleCreationType::class, $article, ['available_models' => $modelIds]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $response = $fastApiClient->request('POST', '/generate-article', [
                    'json' => $serializer->normalize($article, 'json', ['groups' => Article::CREATION_REQUEST_GROUP]),
                ]);

                if (Response::HTTP_OK !== $response->getStatusCode()) {
                    $this->addFlash('danger', 'app_article_create.error.api_ko_response');

                    return $this->render('article_create.html.twig', ['form' => $form]);
                }

                $articleCreationTaskDto = $serializer->deserialize(
                    $response->getContent(), ArticleCreationTaskDto::class, 'json'
                );
                $errors = $validator->validate($articleCreationTaskDto);
                if (count($errors) > 0) {
                    $this->addFlash('danger', 'app_article_create.error.api_invalid_response');

                    return $this->render('article_create.html.twig', ['form' => $form]);
                }

                $article->setGenerationTaskId($articleCreationTaskDto->getId());

                $entityManager->persist($article);
                $entityManager->flush();

                return $this->redirectToRoute(
                    'article_waiting_page', ['id' => $article->getId()]
                );
            } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
                $this->addFlash('danger', 'app_article_create.error.api_exception');
                $logger->error($e);
            }
        }

        return $this->render('article_create.html.twig', ['form' => $form]);
    }

    #[Route('/app/{id}/wait-for-generation', name: 'article_waiting_page')]
    #[IsGranted(ArticleVoter::USER_CAN_VIEW_ARTICLE, subject: 'article')]
    public function waitForArticleGeneration(Article $article): Response
    {
        return $this->render('article_waiting_page.html.twig', ['article' => $article]);
    }

    #[Route('/app/{id}/check-for-status', name: 'article_check_status', options: ['expose' => true])]
    #[IsGranted(ArticleVoter::USER_CAN_VIEW_ARTICLE, subject: 'article')]
    public function checkArticleStatus(
        Article $article,
        HttpClientInterface $fastApiClient,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        LoggerInterface $logger,
    ): JsonResponse {
        try {
            $response = $fastApiClient->request('GET', sprintf('/article-status/%s', $article->getGenerationTaskId()));

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                return $this->json(['error' => true], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $articleProgressStatusDto = $serializer->deserialize(
                $response->getContent(), ArticleProgressStatusDto::class, 'json'
            );
            $errors = $validator->validate($articleProgressStatusDto);
            if (count($errors) > 0) {
                return $this->json(['error' => true], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if (Article::ARTICLE_GENERATION_TASK_STATUS_SUCCESS === $articleProgressStatusDto->getTaskStatus()) {
                $article->setArticleGeneratedAt(new DateTimeImmutable());
                $entityManager->flush();
            }

            return $this->json($articleProgressStatusDto);
        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            $logger->error($e);

            return $this->json(['error' => true], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/app/{id}/detail', name: 'article_detail_page', options: ['expose' => true])]
    #[IsGranted(ArticleVoter::USER_CAN_VIEW_ARTICLE, subject: 'article')]
    public function viewArticle(
        Article $article,
        ArticleService $articleService,
        HttpClientInterface $fastApiClient,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        LoggerInterface $logger,
    ): Response {
        if (!$article->getArticleGeneratedAt() instanceof DateTimeInterface) {
            try {
                $response = $fastApiClient->request('GET', sprintf('/article-status/%s', $article->getGenerationTaskId()));

                if (Response::HTTP_OK !== $response->getStatusCode()) {
                    return $this->render('article_detail.html.twig', [
                        'article' => $article,
                        'error' => true,
                    ]);
                }

                $articleProgressStatusDto = $serializer->deserialize(
                    $response->getContent(), ArticleProgressStatusDto::class, 'json'
                );
                $errors = $validator->validate($articleProgressStatusDto);
                if (count($errors) > 0) {
                    return $this->render('article_detail.html.twig', [
                        'article' => $article,
                        'error' => true,
                    ]);
                }

                if (Article::ARTICLE_GENERATION_TASK_STATUS_SUCCESS === $articleProgressStatusDto->getTaskStatus()) {
                    $article->setArticleGeneratedAt(new DateTimeImmutable());
                    $entityManager->flush();
                } elseif (in_array($articleProgressStatusDto->getTaskStatus(), [
                    Article::ARTICLE_GENERATION_TASK_STATUS_PENDING,
                    Article::ARTICLE_GENERATION_TASK_STATUS_STARTED,
                    Article::ARTICLE_GENERATION_TASK_STATUS_PROGRESS,
                ])) {
                    // TODO: pending status => check on s3 if folder exists and is complete

                    return $this->redirectToRoute('article_waiting_page', ['id' => $article->getId()]);
                } else {
                    return $this->render('article_detail.html.twig', [
                        'article' => $article,
                        'error' => true,
                    ]);
                }
            } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
                $logger->error($e);

                return $this->render('article_detail.html.twig', [
                    'article' => $article,
                    'error' => true,
                ]);
            }
        }

        $error = false;
        if (null === $article->getContent()) {
            try {
                $articleService->buildAndSaveArticleContent($article);
            } catch (CommonMarkException|Exception) {
                $error = true;
            }
        }

        return $this->render('article_detail.html.twig', [
            'article' => $article,
            'error' => $error,
        ]);
    }
}
