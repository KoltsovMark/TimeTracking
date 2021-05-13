<?php

declare(strict_types=1);

namespace App\Controller\Api\Task;

use App\Controller\Api\BaseController;
use App\Dto\Api\Form\Task\CreateTaskTypeDto;
use App\Factory\Api\Task\Dto\CreateTaskDtoFactory;
use App\Form\Api\Task\CreateTaskType;
use App\Service\Task\CreateTaskService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CreateController extends BaseController
{
    private CreateTaskDtoFactory $createTaskDtoFactory;
    private CreateTaskService $createTaskService;

    public function __construct(CreateTaskDtoFactory $createTaskDtoFactory, CreateTaskService $createTaskService)
    {
        $this->createTaskDtoFactory = $createTaskDtoFactory;
        $this->createTaskService = $createTaskService;
    }

    /**
     * @Route("tasks", name="tasks_create", methods={"POST"})
     * @Security("is_granted('ROLE_TASKS_CREATOR')")
     *
     * @Rest\View(statusCode=201)
     */
    public function new(Request $request)
    {
        $form = $this->createApiForm(CreateTaskType::class, $request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $form;
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

        // @todo add Resource instead of entity
        return $task;
    }
}
