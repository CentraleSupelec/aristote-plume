<?php

namespace App\Model;

use App\Constants;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleProgressStageInfoDto
{
    #[Assert\Choice(callback: [Constants::class, 'getAvailableArticleProgressStages'])]
    private ?string $stage = null;

    #[Assert\GreaterThan(value: 0)]
    private ?int $totalStageCount = null;

    #[Assert\GreaterThanOrEqual(value: 0)]
    private ?int $stageNumber = null;

    private ?string $stageStartDate = null;

    public function getStage(): ?string
    {
        return $this->stage;
    }

    public function setStage(?string $stage): static
    {
        $this->stage = $stage;

        return $this;
    }

    public function getTotalStageCount(): ?int
    {
        return $this->totalStageCount;
    }

    public function setTotalStageCount(?int $totalStageCount): static
    {
        $this->totalStageCount = $totalStageCount;

        return $this;
    }

    public function getStageNumber(): ?int
    {
        return $this->stageNumber;
    }

    public function setStageNumber(?int $stageNumber): static
    {
        $this->stageNumber = $stageNumber;

        return $this;
    }

    public function getStageStartDate(): ?string
    {
        return $this->stageStartDate;
    }

    public function setStageStartDate(?string $stageStartDate): static
    {
        $this->stageStartDate = $stageStartDate;

        return $this;
    }
}
