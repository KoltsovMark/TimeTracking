<?php

declare(strict_types=1);

namespace App\Dto\Api\Task;

use App\Entity\Task\Task;

class TasksReportDataDto
{
    private int $totalTasks;
    private int $totalTime;
    /**
     * @var Task[]
     */
    private array $tasks;
    private string $reportFileName;

    public function getTotalTasks(): int
    {
        return $this->totalTasks;
    }

    public function setTotalTasks(int $totalTasks): TasksReportDataDto
    {
        $this->totalTasks = $totalTasks;

        return $this;
    }

    public function getTotalTime(): int
    {
        return $this->totalTime;
    }

    public function setTotalTime(int $totalTime): TasksReportDataDto
    {
        $this->totalTime = $totalTime;

        return $this;
    }

    /**
     * @return Task[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    /**
     * @param Task[] $tasks
     */
    public function setTasks(array $tasks): TasksReportDataDto
    {
        $this->tasks = $tasks;

        return $this;
    }

    public function getReportFileName(): string
    {
        return $this->reportFileName;
    }

    public function setReportFileName(string $reportFileName): TasksReportDataDto
    {
        $this->reportFileName = $reportFileName;

        return $this;
    }
}
