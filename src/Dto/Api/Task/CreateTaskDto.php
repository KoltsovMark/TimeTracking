<?php

declare(strict_types=1);

namespace App\Dto\Api\Task;

use App\Entity\User;
use DateTime;

class CreateTaskDto
{
    private string $title;
    private ?string $comment = null;
    private int $timeSpent;
    private DateTime $date;
    private User $user;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): CreateTaskDto
    {
        $this->title = $title;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): CreateTaskDto
    {
        $this->comment = $comment;

        return $this;
    }

    public function getTimeSpent(): int
    {
        return $this->timeSpent;
    }

    public function setTimeSpent(int $timeSpent): CreateTaskDto
    {
        $this->timeSpent = $timeSpent;

        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): CreateTaskDto
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): CreateTaskDto
    {
        $this->user = $user;

        return $this;
    }
}
