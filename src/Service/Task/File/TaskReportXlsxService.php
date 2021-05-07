<?php

declare(strict_types=1);

namespace App\Service\Task\File;

use App\Contract\Task\File\TaskFileReportInterface;
use App\Dto\Api\Task\TasksReportDataDto;
use App\Entity\Task;
use App\Service\File\CsvWriterService;
use App\Service\File\XlsxWriterService;
use App\Traits\Task\File\SimpleTaskReportTableTrait;

/**
 * Class TaskReportXlsxService
 * @package App\Service\Task\File
 */
class TaskReportXlsxService extends AbstractTaskReportService
{
    use SimpleTaskReportTableTrait;

    /**
     *
     */
    public const REPORTS_SUB_PATH = '/excel';

    /**
     * TaskReportXlsxService constructor.
     *
     * @param XlsxWriterService $xlsxWriterService
     */
    public function __construct(XlsxWriterService $xlsxWriterService)
    {
        $this->setWriterService($xlsxWriterService);
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return self::REPORTS_PATH . self::REPORTS_SUB_PATH;
    }
}