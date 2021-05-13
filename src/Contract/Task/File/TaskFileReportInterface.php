<?php

declare(strict_types=1);

namespace App\Contract\Task\File;

use App\Dto\Api\Task\TasksReportDataDto;
use App\Service\File\AbstractWriterService;
use App\Service\Task\File\AbstractTaskReportService;

interface TaskFileReportInterface
{
    /*
     * Prepare tasks report data for writer service and store to file
     */
    public function generate(TasksReportDataDto $tasksReportDataDto): string;

    /**
     * Return writer service for specific report format, e.g. csf.
     */
    public function getWriterService(): AbstractWriterService;

    public function setWriterService(AbstractWriterService $writerService): AbstractTaskReportService;
}
