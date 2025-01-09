<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\PlumeUser;
use App\Form\ArticleCreationType;
use App\Model\ArticleCreationTaskDto;
use Doctrine\ORM\EntityManagerInterface;
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
        Request                  $request,
        EntityManagerInterface   $entityManager,
        HttpClientInterface      $fastApiClient,
        SerializerInterface      $serializer,
        ValidatorInterface       $validator,
    ): Response
    {
        $article = (new Article())->setAuthor($user);
        $form = $this->createForm(ArticleCreationType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $response = $fastApiClient->request('POST', '/generate-article', [
                    'json' => $serializer->normalize($article, 'json', ['groups' => Article::CREATION_REQUEST_GROUP]),
                ]);

                if (Response::HTTP_OK !== $response->getStatusCode()) {
                    // TODO: handle failure (flash message)
                }

                $articleCreationTaskDto = $serializer->deserialize(
                    $response->getContent(), ArticleCreationTaskDto::class, 'json'
                );
                $errors = $validator->validate($articleCreationTaskDto);
                if (count($errors) > 0) {
                    // TODO: handle errors
                }

                $article->setGenerationTaskId($articleCreationTaskDto->getId());

                $entityManager->persist($article);
                $entityManager->flush();

                return $this->redirectToRoute(
                    'article_waiting_page', ['id' => $article->getId()]
                );
            } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface) {
                // TODO: handle errors
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
    public function checkArticleStatus(Article $article): JsonResponse
    {
        return $this->json([
            "task_id" => "a7ec25ee-dba2-48f8-ae9c-d6c572bd368b",
            "task_status" => "PROGRESS",
            "stage_info" => [
                "stage" => "article.progress.knowledge_curation",
                "total_stages" => 5,
                "stage_number" => 1,
                "stage_start_date" => "2025-01-09 15:02:52"
            ]
        ]);
    }

    #[Route('/{id}', name: 'article_detail_page')]
    public function viewArticle(): Response
    {
        return new Response('ok');
    }
}
