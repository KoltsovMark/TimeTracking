<?php

declare(strict_types=1);

namespace App\Factory\Api\Task\File;

use App\Contract\File\FileWriterInterface;
use App\Exception\Factory\UnsupportedFactoryObject;
use App\Service\File\PdfWriterService;
use App\Service\Task\File\TaskReportCsvService;
use App\Service\Task\File\TaskReportPdfService;
use App\Service\Task\File\TaskReportXlsxService;

class TaskReportServiceFactory
{
    private TaskReportPdfService $taskReportPdfService;
    private TaskReportCsvService $taskReportCsvService;
    private TaskReportXlsxService $taskReportXlsxService;

    public function __construct(
        TaskReportPdfService $taskReportPdfService,
        TaskReportCsvService $taskReportCsvService,
        TaskReportXlsxService $taskReportXlsxService
    ) {
        $this->taskReportPdfService = $taskReportPdfService;
        $this->taskReportCsvService = $taskReportCsvService;
        $this->taskReportXlsxService = $taskReportXlsxService;
    }

    /**
     * @throws UnsupportedFactoryObject
     */
    public function create(string $type)
    {
        // @todo move type to constants
        switch ($type) {
            case 'pdf':
                return $this->taskReportPdfService;
            case 'csv':
                return $this->taskReportCsvService;
            case 'excel':
                return $this->taskReportXlsxService;
            default:
                throw new UnsupportedFactoryObject();
        }
    }
}