<?php

namespace App\Security\Administrator;

use App\Entity\Administrator;
use App\Repository\AdministratorRepository;
use Drenso\OidcBundle\Exception\OidcException;
use Drenso\OidcBundle\Model\OidcUserData;
use Drenso\OidcBundle\Security\UserProvider\OidcUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class AdministratorProvider implements OidcUserProviderInterface
{
    public function __construct(private AdministratorRepository $administratorRepository)
    {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$this->supportsClass($user::class)) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', Administrator::class, $user::class));
        }

        /* @var Administrator $user */
        if (null === $reloadedUser = $this->administratorRepository->findOneBy(['id' => $user->getId()])) {
            throw new UserNotFoundException(sprintf('[Administrator] User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    public function supportsClass(string $class): bool
    {
        return Administrator::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $administrator = $this->administratorRepository->findOneBy(['email' => $identifier]);

        if (!$administrator instanceof Administrator) {
            throw new UserNotFoundException(sprintf('[Administrator] Username "%s" does not exist.', $identifier));
        }

        return $administrator;
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
