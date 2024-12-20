<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\PlumeUser;
use App\Form\ArticleCreationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/app')]
#[IsGranted(PlumeUser::ROLE_DEFAULT)]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(#[CurrentUser] PlumeUser $user, Request $request): Response
    {
        $article = (new Article())->setAuthor($user);
        $form = $this->createForm(ArticleCreationType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($form);
        }

        return $this->render('app_home.html.twig', ['form' => $form]);
    }
}
