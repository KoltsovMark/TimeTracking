<?php

declare(strict_types=1);

namespace App\Service\Task\File;

use App\Service\File\CsvWriterService;
use App\Traits\Task\File\SimpleTaskReportTableTrait;

/**
 * Class TaskReportCsvService
 * @package App\Service\Task\File
 */
class TaskReportCsvService extends AbstractTaskReportService
{
    use SimpleTaskReportTableTrait;

    public const REPORTS_SUB_PATH = '/csv';

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
     * @return string
     */
    protected function getPath(): string
    {
        return self::REPORTS_PATH . self::REPORTS_SUB_PATH;
    }
}