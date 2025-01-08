<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user->isEnabled()) {
            throw new DisabledException();
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
