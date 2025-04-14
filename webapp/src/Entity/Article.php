<?php

namespace App\Entity;

use App\Constants;
use App\Repository\ArticleRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[UniqueEntity(fields: ['generationTaskId'])]
class Article
{
    use TimestampableEntity;
    final public const ARTICLE_TYPE_SCIENCE = 'science';
    final public const ARTICLE_TYPE_ECONOMICS = 'economics';
    final public const ARTICLE_TYPE_LITERATURE = 'literature';
    final public const ARTICLE_LANGUAGE_FR = 'fr';
    final public const ARTICLE_LANGUAGE_EN = 'en';

    final public const ARTICLE_GENERATION_TASK_STATUS_PENDING = 'PENDING';
    final public const ARTICLE_GENERATION_TASK_STATUS_STARTED = 'STARTED';
    final public const ARTICLE_GENERATION_TASK_STATUS_PROGRESS = 'PROGRESS';
    final public const ARTICLE_GENERATION_TASK_STATUS_SUCCESS = 'SUCCESS';
    final public const ARTICLE_GENERATION_TASK_STATUS_FAILURE = 'FAILURE';
    final public const ARTICLE_GENERATION_TASK_STATUS_RETRY = 'RETRY';
    final public const ARTICLE_GENERATION_TASK_STATUS_REVOKED = 'REVOKED';

    final public const CREATION_REQUEST_GROUP = 'CREATION_REQUEST';
    final public const WAIT_FOR_GENERATION_GROUP = 'WAIT_FOR_GENERATION';

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups([self::WAIT_FOR_GENERATION_GROUP])]
    private ?Uuid $id = null;

    #[ORM\Column(type: UuidType::NAME, unique: true, nullable: true)]
    private ?Uuid $generationTaskId = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlumeUser $author = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\NotBlank(allowNull: false)]
    #[Groups([self::CREATION_REQUEST_GROUP, self::WAIT_FOR_GENERATION_GROUP])]
    private ?string $requestedTopic = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    #[Assert\Choice(callback: [Constants::class, 'getAvailableArticleTypes'])]
    #[Groups([self::WAIT_FOR_GENERATION_GROUP])]
    private string $requestedType = self::ARTICLE_TYPE_SCIENCE;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    #[Groups([self::CREATION_REQUEST_GROUP, self::WAIT_FOR_GENERATION_GROUP])]
    private ?string $requestedLanguageModel = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    #[Assert\Choice(callback: [Constants::class, 'getAvailableArticleLanguages'])]
    #[Groups([self::CREATION_REQUEST_GROUP, self::WAIT_FOR_GENERATION_GROUP])]
    private string $requestedLanguage = self::ARTICLE_LANGUAGE_EN;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $articleGeneratedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

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

    public function getArticleGeneratedAt(): ?DateTimeInterface
    {
        return $this->articleGeneratedAt;
    }

    public function setArticleGeneratedAt(?DateTimeImmutable $articleGeneratedAt): static
    {
        $this->articleGeneratedAt = $articleGeneratedAt;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
