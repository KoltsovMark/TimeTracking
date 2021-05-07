<?php

declare(strict_types=1);

namespace App\Contract\Task\File;

use App\Dto\Api\Task\TasksReportDataDto;
use App\Service\File\AbstractWriterService;
use App\Service\Task\File\AbstractTaskReportService;

interface TaskFileReportInterface
{
    /**
     * Generate and store report
     *
     * @param TasksReportDataDto $tasksReportDataDto
     */
    public function generate(TasksReportDataDto $tasksReportDataDto): void;
    public function getWriterService(): AbstractWriterService;
    public function setWriterService(AbstractWriterService $writerService): AbstractTaskReportService;
}