<?php

namespace App\Model;

use App\Constants;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleProgressStatusDto
{
    #[Assert\NotNull]
    private ?string $taskId = null;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [Constants::class, 'getAvailableArticleTaskStatuses'])]
    private ?string $taskStatus = null;

    #[Assert\Valid]
    private ?ArticleProgressStageInfoDto $stageInfo = null;

    public function getTaskId(): ?string
    {
        return $this->taskId;
    }

    public function setTaskId(?string $taskId): static
    {
        $this->taskId = $taskId;

        return $this;
    }

    public function getTaskStatus(): ?string
    {
        return $this->taskStatus;
    }

    public function setTaskStatus(?string $taskStatus): static
    {
        $this->taskStatus = $taskStatus;

        return $this;
    }

    public function getStageInfo(): ?ArticleProgressStageInfoDto
    {
        return $this->stageInfo;
    }

    public function setStageInfo(?ArticleProgressStageInfoDto $stageInfo): static
    {
        $this->stageInfo = $stageInfo;

        return $this;
    }
}
