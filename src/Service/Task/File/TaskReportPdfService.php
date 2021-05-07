<?php

declare(strict_types=1);

namespace App\Service\Task\File;

use App\Dto\Api\Task\TasksReportDataDto;
use App\Service\File\PdfWriterService;
use Twig\Environment;

/**
 * Class TaskReportPdfService
 * @package App\Service\Task\File
 */
class TaskReportPdfService extends AbstractTaskReportService
{
    /**
     *
     */
    public const REPORTS_SUB_PATH = '/pdf';

    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * TaskReportPdfService constructor.
     *
     * @param PdfWriterService $pdfWriterService
     * @param Environment $twig
     */
    public function __construct(PdfWriterService $pdfWriterService, Environment $twig)
    {
        $this->twig = $twig;

        $this->setWriterService($pdfWriterService);
    }

    /**
     * @param TasksReportDataDto $tasksReportDataDto
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function prepareData(TasksReportDataDto $tasksReportDataDto): string
    {
        return $this->twig->render('task/report/task_report_pdf.html.twig', [
            'data' => $tasksReportDataDto,
        ]);
    }

    /**
     * @return string
     */
    protected function getFullPath(): string
    {
        return self::REPORTS_PATH . self::REPORTS_SUB_PATH;
    }
}