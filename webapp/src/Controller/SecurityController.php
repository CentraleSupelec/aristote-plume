<?php

namespace App\Controller;

use Drenso\OidcBundle\Exception\OidcCodeChallengeMethodNotSupportedException;
use Drenso\OidcBundle\Exception\OidcConfigurationException;
use Drenso\OidcBundle\Exception\OidcConfigurationResolveException;
use Drenso\OidcBundle\OidcClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SecurityController extends AbstractController
{
    /**
     * @throws OidcConfigurationException|OidcConfigurationResolveException|OidcCodeChallengeMethodNotSupportedException
     */
    #[Route('/admin/login_oidc', name: 'administrator_login_oidc')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function administratorsLoginOidc(OidcClientInterface $administratorOidcClient): RedirectResponse
    {
        return $administratorOidcClient->generateAuthorizationRedirect(null, $this->getParameter('oidc_scopes'));
    }

    #[Route('/admin/forbidden', name: 'administrator_access_denied')]
    public function accessDenied(): Response
    {
        return $this->render('security/access_denied.html.twig');
    }
}
