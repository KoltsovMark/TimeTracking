<?php

declare(strict_types=1);

namespace App\Dto\Api\Task;

use App\Entity\User;
use DateTime;

class GenerateTasksReportDto
{
    private ?DateTime $startDate;
    private ?DateTime $endDate;
    private string $format;
    private User $user;

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTime $startDate): GenerateTasksReportDto
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTime $endDate): GenerateTasksReportDto
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): GenerateTasksReportDto
    {
        $this->format = $format;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): GenerateTasksReportDto
    {
        $this->user = $user;

        return $this;
    }
}
