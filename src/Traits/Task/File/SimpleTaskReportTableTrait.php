<?php

declare(strict_types=1);

namespace App\Traits\Task\File;

use App\Dto\Api\Task\TasksReportDataDto;

trait SimpleTaskReportTableTrait
{
    /**
     * A list of titles for table formats, used in prepareTaskReportData.
     */
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

    /**
     * Prepare tasks report data to store in writer format.
     */
    protected function prepareTaskReportData(TasksReportDataDto $tasksReportDataDto): array
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
