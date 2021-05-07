<?php

namespace App\Dto\Api\Task;

use App\Entity\User;
use DateTime;

class GenerateTasksReportDto
{
    private ?DateTime $startDate;
    private ?DateTime $endDate;
    private string $format;
    private User $user;

    /**
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime|null $startDate
     *
     * @return GenerateTasksReportDto
     */
    public function setStartDate(?DateTime $startDate): GenerateTasksReportDto
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @param DateTime|null $endDate
     *
     * @return GenerateTasksReportDto
     */
    public function setEndDate(?DateTime $endDate): GenerateTasksReportDto
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return GenerateTasksReportDto
     */
    public function setFormat(string $format): GenerateTasksReportDto
    {
        $this->format = $format;
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
     * @return GenerateTasksReportDto
     */
    public function setUser(User $user): GenerateTasksReportDto
    {
        $this->user = $user;
        return $this;
    }
}