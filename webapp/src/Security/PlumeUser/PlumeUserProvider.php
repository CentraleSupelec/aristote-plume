<?php

namespace App\Security\PlumeUser;

use App\Entity\PlumeUser;
use App\Repository\PlumeUserRepository;
use Drenso\OidcBundle\Exception\OidcException;
use Drenso\OidcBundle\Model\OidcUserData;
use Drenso\OidcBundle\Security\UserProvider\OidcUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class PlumeUserProvider implements OidcUserProviderInterface
{
    public function __construct(private PlumeUserRepository $plumeUserRepository)
    {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$this->supportsClass($user::class)) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', PlumeUser::class, $user::class));
        }

        /* @var PlumeUser $user */
        if (null === $reloadedUser = $this->plumeUserRepository->findOneBy(['id' => $user->getId()])) {
            throw new UserNotFoundException(sprintf('[PlumeUser] User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    public function supportsClass(string $class): bool
    {
        return PlumeUser::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $plumeUser = $this->plumeUserRepository->findOneBy(['email' => $identifier]);

        if (!$plumeUser instanceof PlumeUser) {
            throw new UserNotFoundException(sprintf('[PlumeUser] Username "%s" does not exist.', $identifier));
        }

        return $plumeUser;
    }

    public function ensureUserExists(string $userIdentifier, OidcUserData $userData): void
    {
        try {
            $this->loadUserByIdentifier($userIdentifier);
        } catch (UserNotFoundException $exception) {
            throw new OidcException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function loadOidcUser(string $userIdentifier): UserInterface
    {
        return $this->loadUserByIdentifier($userIdentifier);
    }
}
