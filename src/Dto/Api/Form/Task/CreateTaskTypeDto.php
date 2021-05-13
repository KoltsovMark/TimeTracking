<?php

declare(strict_types=1);

namespace App\Dto\Api\Form\Task;

use DateTime;

class CreateTaskTypeDto
{
    private ?string $title;
    private ?string $comment;
    private ?int $timeSpent;
    private ?DateTime $date;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): CreateTaskTypeDto
    {
        $this->title = $title;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): CreateTaskTypeDto
    {
        $this->comment = $comment;

        return $this;
    }

    public function getTimeSpent(): ?int
    {
        return $this->timeSpent;
    }

    public function setTimeSpent(?int $timeSpent): CreateTaskTypeDto
    {
        $this->timeSpent = $timeSpent;

        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): CreateTaskTypeDto
    {
        $this->date = $date;

        return $this;
    }
}
