<?php

namespace App\Controller;

use App\Entity\PlumeUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/app')]
#[IsGranted(PlumeUser::ROLE_DEFAULT)]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('app_home.html.twig');
    }
}
