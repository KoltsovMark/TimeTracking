<?php

declare(strict_types=1);

namespace App\Controller\Api\Task\Report;

use App\Controller\Api\BaseController;
use App\Entity\Task\TasksReport;
use App\Factory\Api\Task\Dto\GenerateTasksReportDtoFactory;
use App\Form\Api\Task\GenerateTasksReportType;
use App\Service\Task\ReportTasksService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation as SWG;
use OpenApi\Annotations as OA;
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
     * @Route("tasks/report", name="tasks_report_create", methods={"POST"})
     * @Security("is_granted('ROLE_TASKS_REPORT_CREATOR')")
     * @Rest\View(statusCode=201)
     *
     * @SWG\Security(name="Bearer")
     * @OA\Post(
     *     tags={"Tasks"},
     *     summary="Create a new task report",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref=@SWG\Model(type=GenerateTasksReportType::class))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created",
     *         @SWG\Model(type=TasksReport::class)
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unathorized request",
     *         @OA\Schema(
     *             @OA\Property(property="code", type="integer", example=401),
     *             @OA\Property(property="message", type="string", example="Expired JWT Token"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation failed",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="code", type="integer", example=400),
     *                 @OA\Property(property="message", type="string", example="Validation Failed"),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object",
     *                     @OA\Property(
     *                         property="children",
     *                         type="object",
     *                         @OA\Property(
     *                             property="format",
     *                             type="array",
     *                             @OA\Items(example="This value is required")
     *                         ),
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
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

        $tasksReport = $this->reportTasksService->generateReport($generateTasksReportDto);

        return $tasksReport;
    }
}
