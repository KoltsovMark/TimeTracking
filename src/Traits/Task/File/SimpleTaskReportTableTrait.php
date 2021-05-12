<?php

declare(strict_types=1);

namespace App\Traits\Task\File;

use App\Dto\Api\Task\TasksReportDataDto;

trait SimpleTaskReportTableTrait
{
    protected function getTitles(): array
    {
        return [
            'Title',
            'Comment',
            'Time',
            'Date',
            'Email',
            'Total Tasks',
            'Total Time Spent',
        ];
    }

    protected function prepareData(TasksReportDataDto $tasksReportDataDto): array
    {
        $data[] = $this->getTitles();

        foreach ($tasksReportDataDto->getTasks() as $task) {
            $data[] = [
                $task->getTitle(),
                $task->getComment(),
                $task->getTimeSpent(),
                $task->getDate()->format('Y-m-d'),
                $task->getUser()->getEmail(),
                null,
                null,
            ];
        }

        $data[] = [
            null,
            null,
            null,
            null,
            null,
            $tasksReportDataDto->getTotalTasks(),
            $tasksReportDataDto->getTotalTime(),
        ];

        return $data;
    }
}
