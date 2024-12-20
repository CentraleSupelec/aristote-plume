<?php

namespace App\Entity;

use App\Constants;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlumeUser $author = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    private ?string $requestedTopic = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    #[Assert\Choice(callback: [Constants::class, 'getAvailableArticleTypes'])]
    private ?string $requestedType = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    #[Assert\Choice(callback: [Constants::class, 'getAvailableArticleGenerationModels'])]
    private ?string $requestedLanguageModel = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    #[Assert\Choice(callback: [Constants::class, 'getAvailableArticleLanguages'])]
    private ?string $requestedLanguage = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getAuthor(): ?PlumeUser
    {
        return $this->author;
    }

    public function setAuthor(?PlumeUser $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getRequestedTopic(): ?string
    {
        return $this->requestedTopic;
    }

    public function setRequestedTopic(string $requestedTopic): static
    {
        $this->requestedTopic = $requestedTopic;

        return $this;
    }

    public function getRequestedType(): ?string
    {
        return $this->requestedType;
    }

    public function setRequestedType(string $requestedType): static
    {
        $this->requestedType = $requestedType;

        return $this;
    }

    public function getRequestedLanguageModel(): ?string
    {
        return $this->requestedLanguageModel;
    }

    public function setRequestedLanguageModel(string $requestedLanguageModel): static
    {
        $this->requestedLanguageModel = $requestedLanguageModel;

        return $this;
    }

    public function getRequestedLanguage(): ?string
    {
        return $this->requestedLanguage;
    }

    public function setRequestedLanguage(string $requestedLanguage): static
    {
        $this->requestedLanguage = $requestedLanguage;

        return $this;
    }
}
