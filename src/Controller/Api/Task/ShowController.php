<?php

declare(strict_types=1);

namespace App\Controller\Api\Task;

use App\Controller\Api\BaseController;
use App\Entity\Task\Task;
use Nelmio\ApiDocBundle\Annotation as SWG;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends BaseController
{
    /**
     * @Route("tasks/{id}", name="tasks_show", requirements={"id"="\d+"}, methods={"GET"})
     * @Security("is_granted('ROLE_TASKS_VIEWER')")
     *
     * @SWG\Security(name="Bearer")
     * @OA\Get(
     *     tags={"Tasks"},
     *     summary="Show task details",
     *     description="Show task details",
     *     @OA\Response(
     *         response=200,
     *         description="Return task details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref=@SWG\Model(type=Task::class))
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
     *         description="Task not found",
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
    public function show(Task $task)
    {
        if ($task->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->successResponse($task);
    }
}
