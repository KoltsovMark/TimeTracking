<?php

declare(strict_types=1);

namespace App\Dto\Api\Task;

use App\Entity\Task;

class TasksReportDataDto
{
    private int $totalTasks;
    private int $totalTime;
    /**
     * @var Task[]
     */
    private array $tasks;
    private string $reportFileName;

    /**
     * @return int
     */
    public function getTotalTasks(): int
    {
        return $this->totalTasks;
    }

    /**
     * @param int $totalTasks
     *
     * @return TasksReportDataDto
     */
    public function setTotalTasks(int $totalTasks): TasksReportDataDto
    {
        $this->totalTasks = $totalTasks;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalTime(): int
    {
        return $this->totalTime;
    }

    /**
     * @param int $totalTime
     *
     * @return TasksReportDataDto
     */
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
     *
     * @return TasksReportDataDto
     */
    public function setTasks(array $tasks): TasksReportDataDto
    {
        $this->tasks = $tasks;
        return $this;
    }

    /**
     * @return string
     */
    public function getReportFileName(): string
    {
        return $this->reportFileName;
    }

    /**
     * @param string $reportFileName
     *
     * @return TasksReportDataDto
     */
    public function setReportFileName(string $reportFileName): TasksReportDataDto
    {
        $this->reportFileName = $reportFileName;
        return $this;
    }
}