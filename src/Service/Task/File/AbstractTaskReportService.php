<?php

declare(strict_types=1);

namespace App\Service\Task\File;

use App\Contract\Task\File\TaskFileReportInterface;
use App\Dto\Api\Task\TasksReportDataDto;
use App\Service\File\AbstractWriterService;

/**
 * Class AbstractTaskReportService.
 */
abstract class AbstractTaskReportService implements TaskFileReportInterface
{
    protected AbstractWriterService $writerService;

    public const REPORTS_PATH = 'reports/tasks';

    /**
     * Prepare tasks report data to store in writer format.
     */
    abstract protected function prepareTaskReportData(TasksReportDataDto $tasksReportDataDto);

    /**
     * Return a path to task report storage folder
     */
    abstract protected function getPath(): string;

    public function getWriterService(): AbstractWriterService
    {
        return $this->writerService;
    }

    public function setWriterService(AbstractWriterService $writerService): AbstractTaskReportService
    {
        $this->writerService = $writerService;

        return $this;
    }

    public function generate(TasksReportDataDto $tasksReportDataDto): string
    {
        $data = $this->prepareTaskReportData($tasksReportDataDto);
        $fileName = $this->getWriterService()->write($tasksReportDataDto->getReportFileName(), $data, $this->getPath());

        return $fileName;
    }
}
