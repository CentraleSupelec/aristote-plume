<?php

namespace App\Model;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleCreationTaskDto
{
    #[Assert\NotBlank(allowNull: false)]
    private ?Uuid $id = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }
}
