<?php

declare(strict_types=1);

namespace App\Controller\Api\Task;

use App\Controller\Api\BaseController;
use App\Factory\Api\Task\Dto\GenerateTasksReportDtoFactory;
use App\Form\Api\Task\GenerateTasksReportType;
use App\Service\Task\ReportTasksService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CreateReportController extends BaseController
{
    private GenerateTasksReportDtoFactory $generateTasksReportDtoFactory;
    private ReportTasksService $reportTasksService;

    public function __construct(
        GenerateTasksReportDtoFactory $generateTasksReportDtoFactory,
        ReportTasksService $reportTasksService
    ) {
        $this->generateTasksReportDtoFactory = $generateTasksReportDtoFactory;
        $this->reportTasksService = $reportTasksService;
    }

    /**
     * @Route("tasks/report", name="tasks_generate", methods={"POST"})
     * @Security("is_granted('ROLE_TASKS_REPORT_CREATOR')")
     *
     * @Rest\View(statusCode=201)
     */
    public function new(Request $request)
    {
        /**
         * @todo Add queue for background generation
         * @todo add additional endpoint for show file to improve usability
         */
        $form = $this->createApiForm(GenerateTasksReportType::class, $request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $form;
        }

        $generateTasksReportDto = $this->generateTasksReportDtoFactory->createFromArray(
            \array_merge($form->getData(), ['user' => $this->getUser()])
        );

        $reportPath = $this->reportTasksService->generateReport($generateTasksReportDto);

        return [
            'content' => base64_encode(file_get_contents($reportPath)),
            'extension' => pathinfo($reportPath)['extension'],
        ];
    }
}
