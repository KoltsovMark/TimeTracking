<?php

declare(strict_types=1);

namespace App\Service\Task\File;

use App\Contract\Task\File\TaskFileReportInterface;
use App\Dto\Api\Task\TasksReportDataDto;
use App\Entity\Task;
use App\Service\File\CsvWriterService;
use App\Service\File\XlsxWriterService;

class TaskReportXlsxService extends AbstractTaskReportService
{
    public const REPORTS_SUB_PATH = '/excel';

    public function __construct(XlsxWriterService $xlsxWriterService)
    {
        $this->setWriterService($xlsxWriterService);
    }

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

    protected function getFullPath(): string
    {
        return self::REPORTS_PATH . self::REPORTS_SUB_PATH;
    }
}