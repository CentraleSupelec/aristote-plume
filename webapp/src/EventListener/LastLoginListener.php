<?php

namespace App\EventListener;

use App\Entity\Administrator;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

#[AsEventListener(event: 'security.interactive_login')]
readonly class LastLoginListener
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof Administrator) {
            $user->setLastLoginAt(new DateTimeImmutable());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}
