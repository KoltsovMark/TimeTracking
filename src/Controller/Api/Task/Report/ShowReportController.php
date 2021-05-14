<?php

declare(strict_types=1);

namespace App\Controller\Api\Task\Report;

use App\Controller\Api\BaseController;
use App\Entity\Task\TasksReport;
use Nelmio\ApiDocBundle\Annotation as SWG;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ShowReportController extends BaseController
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @Route("tasks/report/{id}", name="tasks_report_show", requirements={"id"="\d+"}, methods={"GET"})
     * @Security("is_granted('ROLE_TASKS_REPORT_VIEWER')")
     *
     * @SWG\Security(name="Bearer")
     * @OA\Get(
     *     tags={"Tasks"},
     *     summary="Show report content",
     *     description="Show report content",
     *     @OA\Response(
     *         response=200,
     *         description="Report exist and returned",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                @OA\Property(property="content", type="string", example="VGl0bGUsQ29tbWVudCxUaW1lLERhdGUsRW1haWwsVG90YWwgVGFza3MsVG90YWwgVGltZSBTcGVudA0sLCwsLDAsMA0"),
     *                @OA\Property(property="extension", type="string", example="csv")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unathorized request",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="code", type="integer", example=401),
     *                 @OA\Property(property="message", type="string", example="Expired JWT Token"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="code", type="integer", example=403),
     *                 @OA\Property(property="message", type="string", example="Access Denied."),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Report not found",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="code", type="integer", example=404),
     *                 @OA\Property(property="message", type="string", example="Not Found")
     *             )
     *         )
     *     )
     * )
     */
    public function show(TasksReport $tasksReport)
    {
        if ($tasksReport->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->filesystem->exists($tasksReport->getStorageFullPath())) {
            throw $this->createNotFoundException();
        }

        return $this->successResponse(
            [
                'content' => base64_encode(file_get_contents($tasksReport->getStorageFullPath())),
                'extension' => pathinfo($tasksReport->getStorageFullPath())['extension'],
            ]
        );
    }
}
