<?php

declare(strict_types=1);

namespace App\Service\Task\File;

use App\Dto\Api\Task\TasksReportDataDto;
use App\Service\File\PdfWriterService;
use Twig\Environment;

/**
 * Class TaskReportPdfService.
 */
class TaskReportPdfService extends AbstractTaskReportService
{
    public const REPORTS_SUB_PATH = '/pdf';

    private Environment $twig;

    /**
     * TaskReportPdfService constructor.
     */
    public function __construct(PdfWriterService $pdfWriterService, Environment $twig)
    {
        $this->twig = $twig;

        $this->setWriterService($pdfWriterService);
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function prepareTaskReportData(TasksReportDataDto $tasksReportDataDto): string
    {
        return $this->twig->render('task/report/task_report_pdf.html.twig', [
            'data' => $tasksReportDataDto,
        ]);
    }

    protected function getPath(): string
    {
        return self::REPORTS_PATH.self::REPORTS_SUB_PATH;
    }
}
