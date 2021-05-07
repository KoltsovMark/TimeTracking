<?php

declare(strict_types=1);

namespace App\Service\Task\File;

use App\Contract\Task\File\TaskFileReportInterface;
use App\Dto\Api\Task\TasksReportDataDto;
use App\Service\File\AbstractWriterService;

/**
 * Class AbstractTaskReportService
 * @package App\Service\Task\File
 */
abstract class AbstractTaskReportService implements TaskFileReportInterface
{
    /**
     * @var AbstractWriterService
     */
    protected AbstractWriterService $writerService;

    public const REPORTS_PATH = 'reports/tasks';

    /**
     * @param TasksReportDataDto $tasksReportDataDto
     *
     * @return mixed
     */
    abstract protected function prepareData(TasksReportDataDto $tasksReportDataDto);

    /**
     * @return string
     */
    abstract protected function getFullPath(): string;

    /**
     * @return AbstractWriterService
     */
    public function getWriterService(): AbstractWriterService
    {
        return $this->writerService;
    }

    /**
     * @param AbstractWriterService $writerService
     *
     * @return AbstractTaskReportService
     */
    public function setWriterService(AbstractWriterService $writerService): AbstractTaskReportService
    {
        $this->writerService = $writerService;
        return $this;
    }

    /**
     * @param TasksReportDataDto $tasksReportDataDto
     */
    public function generate(TasksReportDataDto $tasksReportDataDto): void
    {
        $data = $this->prepareData($tasksReportDataDto);
        $this->getWriterService()->write($tasksReportDataDto->getReportFileName(), $data, $this->getFullPath());
    }
}