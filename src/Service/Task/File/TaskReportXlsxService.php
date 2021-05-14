<?php

declare(strict_types=1);

namespace App\Service\Task\File;

use App\Service\File\XlsxWriterService;
use App\Traits\Task\File\SimpleTaskReportTableTrait;

/**
 * Class TaskReportXlsxService.
 */
class TaskReportXlsxService extends AbstractTaskReportService
{
    use SimpleTaskReportTableTrait;

    public const REPORTS_SUB_PATH = '/excel';

    /**
     * TaskReportXlsxService constructor.
     */
    public function __construct(XlsxWriterService $xlsxWriterService)
    {
        $this->setWriterService($xlsxWriterService);
    }

    protected function getPath(): string
    {
        return self::REPORTS_PATH.self::REPORTS_SUB_PATH;
    }
}
