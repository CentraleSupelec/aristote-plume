<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\PlumeUser;
use App\Form\ArticleCreationType;
use App\Model\ArticleCreationTaskDto;
use App\Model\ArticleProgressStatusDto;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/app')]
#[IsGranted(PlumeUser::ROLE_DEFAULT)]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        #[CurrentUser] PlumeUser $user,
        Request $request,
        EntityManagerInterface $entityManager,
        HttpClientInterface $fastApiClient,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        LoggerInterface $logger,
    ): Response {
        $article = (new Article())->setAuthor($user);
        $form = $this->createForm(ArticleCreationType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $response = $fastApiClient->request('POST', '/generate-article', [
                    'json' => $serializer->normalize($article, 'json', ['groups' => Article::CREATION_REQUEST_GROUP]),
                ]);

                if (Response::HTTP_OK !== $response->getStatusCode()) {
                    $this->addFlash('danger', 'app_home.error.api_ko_response');

                    return $this->render('app_home.html.twig', ['form' => $form]);
                }

                $articleCreationTaskDto = $serializer->deserialize(
                    $response->getContent(), ArticleCreationTaskDto::class, 'json'
                );
                $errors = $validator->validate($articleCreationTaskDto);
                if (count($errors) > 0) {
                    $this->addFlash('danger', 'app_home.error.api_invalid_response');

                    return $this->render('app_home.html.twig', ['form' => $form]);
                }

                $article->setGenerationTaskId($articleCreationTaskDto->getId());

                $entityManager->persist($article);
                $entityManager->flush();

                return $this->redirectToRoute(
                    'article_waiting_page', ['id' => $article->getId()]
                );
            } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
                $this->addFlash('danger', 'app_home.error.api_exception');
                $logger->error($e);
            }
        }

        return $this->render('app_home.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/wait-for-generation', name: 'article_waiting_page')]
    public function waitForArticleGeneration(Article $article): Response
    {
        return $this->render('article_waiting_page.html.twig', ['article' => $article]);
    }

    #[Route('/{id}/check-for-status', name: 'article_check_status', options: ['expose' => true])]
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

    #[Route('/{id}', name: 'article_detail_page', options: ['expose' => true])]
    public function viewArticle(Article $article): Response
    {
        return new Response('ok');
    }
}
