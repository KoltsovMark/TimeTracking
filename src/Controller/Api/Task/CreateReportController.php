<?php

namespace App\Controller\Api\Task;

use App\Factory\Api\Task\Dto\GenerateTasksReportDtoFactory;
use App\Form\Api\Task\GenerateTasksReportType;
use App\Service\Task\ReportTasksService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("tasks/report", name="tasks_report")
 */
class CreateReportController extends AbstractFOSRestController
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
     * @Route("/generate", name="_generate", methods={"POST"})
     * @Security("is_granted('ROLE_TASKS_REPORT_CREATOR')")
     *
     * @Rest\View(statusCode=200)
     */
    public function generateReport(Request $request)
    {
        /**
         * @todo Add queue for background generation
         * @todo add additional endpoint for show file to improve usability
         */
        $form = $this->createForm(GenerateTasksReportType::class);
        $form->submit($request->query->all());

        if ( ! $form->isSubmitted() || ! $form->isValid()) {
            return $form;
        }

        $generateTasksReportDto = $this->generateTasksReportDtoFactory->createFromArray(
            \array_merge($form->getData(), ['user' => $this->getUser()])
        );

        $reportPath = $this->reportTasksService->generateReport($generateTasksReportDto);

        return [
            'content' => base64_encode(file_get_contents($reportPath)),
            'extension' => pathinfo($reportPath)['extension']
        ];
    }
}