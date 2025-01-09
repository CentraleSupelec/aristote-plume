<?php

namespace App\Entity;

use App\Repository\AdministratorRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Stringable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdministratorRepository::class)]
#[UniqueEntity(fields: ['email'])]
class Administrator implements UserInterface, Stringable
{
    use TimestampableEntity;

    final public const ROLE_DEFAULT = 'ROLE_SUPER_ADMIN';

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'citext', length: 255, unique: true)]
    #[Assert\Email]
    #[Assert\NotBlank(allowNull: false)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $lastLoginAt = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $enabled = null;

    public function __toString(): string
    {
        return (string) $this->getEmail();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getLastLoginAt(): ?DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?DateTimeInterface $lastLoginAt): static
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getRoles(): array
    {
        return [static::ROLE_DEFAULT];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
