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

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return CreateTaskDto
     */
    public function setTitle(string $title): CreateTaskDto
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     *
     * @return CreateTaskDto
     */
    public function setComment(?string $comment): CreateTaskDto
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeSpent(): int
    {
        return $this->timeSpent;
    }

    /**
     * @param int $timeSpent
     *
     * @return CreateTaskDto
     */
    public function setTimeSpent(int $timeSpent): CreateTaskDto
    {
        $this->timeSpent = $timeSpent;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return CreateTaskDto
     */
    public function setDate(DateTime $date): CreateTaskDto
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return CreateTaskDto
     */
    public function setUser(User $user): CreateTaskDto
    {
        $this->user = $user;
        return $this;
    }
}