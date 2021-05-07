<?php

declare(strict_types=1);

namespace App\Service\Task\File;

use App\Dto\Api\Task\TasksReportDataDto;
use App\Service\File\CsvWriterService;

/**
 * Class TaskReportCsvService
 * @package App\Service\Task\File
 */
class TaskReportCsvService extends AbstractTaskReportService
{
    public const REPORTS_SUB_PATH = '/csv';
    public const TITLES = [
        'Title',
        'Comment',
        'Time',
        'Date',
        'Email',
        'Total Tasks',
        'Total Time Spent',
    ];

    /**
     * TaskReportCsvService constructor.
     *
     * @param CsvWriterService $csvWriterService
     */
    public function __construct(CsvWriterService $csvWriterService)
    {
        $this->setWriterService($csvWriterService);
    }

    /**
     * @param TasksReportDataDto $tasksReportDataDto
     *
     * @return array
     */
    protected function prepareData(TasksReportDataDto $tasksReportDataDto): array
    {
        $data[] = self::TITLES;
        $data[] = [
            null,
            null,
            null,
            null,
            null,
            $tasksReportDataDto->getTotalTasks(),
            $tasksReportDataDto->getTotalTime()
        ];

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

        return $data;
    }

    /**
     * @return string
     */
    protected function getFullPath(): string
    {
        return self::REPORTS_PATH . self::REPORTS_SUB_PATH;
    }
}