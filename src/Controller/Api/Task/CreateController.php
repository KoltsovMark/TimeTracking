<?php

declare(strict_types=1);

namespace App\Controller\Api\Task;

use App\Controller\Api\BaseController;
use App\Dto\Api\Form\Task\CreateTaskTypeDto;
use App\Entity\Task\Task;
use App\Factory\Api\Task\Dto\CreateTaskDtoFactory;
use App\Form\Api\Task\CreateTaskType;
use App\Service\Task\CreateTaskService;
use Nelmio\ApiDocBundle\Annotation as SWG;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CreateController extends BaseController
{
    private CreateTaskDtoFactory $createTaskDtoFactory;
    private CreateTaskService $createTaskService;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        CreateTaskDtoFactory $createTaskDtoFactory,
        CreateTaskService $createTaskService,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->createTaskDtoFactory = $createTaskDtoFactory;
        $this->createTaskService = $createTaskService;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("tasks", name="tasks_create", methods={"POST"})
     * @Security("is_granted('ROLE_TASKS_CREATOR')")
     *
     * @SWG\Security(name="Bearer")
     * @OA\Post(
     *     tags={"Tasks"},
     *     summary="Create a new task",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref=@SWG\Model(type=CreateTaskType::class))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created",
     *         @SWG\Model(type=Task::class)
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
     *         response=422,
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
     *                             property="title",
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
        $form = $this->createApiForm(CreateTaskType::class, $request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->failedValidationResponse($form);
        }

        /**
         * @var CreateTaskTypeDto $createTaskTypeDto
         */
        $createTaskTypeDto = $form->getData();

        $createTaskDto = $this->createTaskDtoFactory->createFromCreateTaskTypeDto(
            $createTaskTypeDto,
            $this->getUser()
        );

        $task = $this->createTaskService->createTask($createTaskDto);
        $url = $this->urlGenerator->generate(
            'tasks_show',
            ['id' => $task->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->createdResponse($task, $url);
    }
}
