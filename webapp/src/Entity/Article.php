<?php

namespace App\Entity;

use App\Constants;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[UniqueEntity(fields: ['generationTaskId'])]
class Article
{
    final public const ARTICLE_TYPE_SCIENCE = 'science';
    final public const ARTICLE_TYPE_ECONOMICS = 'economics';
    final public const ARTICLE_TYPE_LITERATURE = 'literature';
    final public const ARTICLE_LANGUAGE_FR = 'fr';
    final public const ARTICLE_LANGUAGE_EN = 'en';

    final public const CREATION_REQUEST_GROUP = 'CREATION_REQUEST';

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: UuidType::NAME, unique: true, nullable: true)]
    private ?Uuid $generationTaskId = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlumeUser $author = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    #[Groups([self::CREATION_REQUEST_GROUP])]
    private ?string $requestedTopic = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    #[Assert\Choice(callback: [Constants::class, 'getAvailableArticleTypes'])]
    private string $requestedType = self::ARTICLE_TYPE_SCIENCE;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    #[Assert\Choice(callback: [Constants::class, 'getAvailableArticleGenerationModels'])]
    #[Groups([self::CREATION_REQUEST_GROUP])]
    private ?string $requestedLanguageModel = 'casperhansen/llama-3-70b-instruct-awq';

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    #[Assert\Choice(callback: [Constants::class, 'getAvailableArticleLanguages'])]
    #[Groups([self::CREATION_REQUEST_GROUP])]
    private string $requestedLanguage = self::ARTICLE_LANGUAGE_EN;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getGenerationTaskId(): ?Uuid
    {
        return $this->generationTaskId;
    }

    public function setGenerationTaskId(?Uuid $generationTaskId): static
    {
        $this->generationTaskId = $generationTaskId;

        return $this;
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

    public function getRequestedType(): string
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

    public function setRequestedLanguageModel(?string $requestedLanguageModel): static
    {
        $this->requestedLanguageModel = $requestedLanguageModel;

        return $this;
    }

    public function getRequestedLanguage(): string
    {
        return $this->requestedLanguage;
    }

    public function setRequestedLanguage(string $requestedLanguage): static
    {
        $this->requestedLanguage = $requestedLanguage;

        return $this;
    }
}
