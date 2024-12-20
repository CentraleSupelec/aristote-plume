<?php

namespace App\Controller\Sonata;

use App\Entity\PlumeUser;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PlumeUserController extends CRUDController
{
    public function plumeUserAdminImpersonateAction(): RedirectResponse
    {
        /** @var PlumeUser $plumeUser */
        $plumeUser = $this->admin->getSubject();

        return $this->redirectToRoute('app_home', ['_switch_user' => $plumeUser->getEmail()]);
    }
}
