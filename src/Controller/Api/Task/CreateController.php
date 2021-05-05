<?php

declare(strict_types=1);

namespace App\Controller\Api\Task;

use App\Factory\Api\Task\Dto\CreateTaskDtoFactory;
use App\Form\Api\Task\CreateTaskType;
use App\Service\Task\CreateTaskService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("tasks/", name="tasks_")
 */
class CreateController extends AbstractFOSRestController
{
    private CreateTaskDtoFactory $createTaskDtoFactory;
    private CreateTaskService $createTaskService;

    public function __construct(CreateTaskDtoFactory $createTaskDtoFactory, CreateTaskService $createTaskService)
    {
        $this->createTaskDtoFactory = $createTaskDtoFactory;
        $this->createTaskService = $createTaskService;
    }

    /**
     * @Route("create", name="create", methods={"POST"})
     * @Security("is_granted('ROLE_TASKS_CREATOR')")
     *
     * @Rest\View(statusCode=201)
     */
    public function createTask(Request $request)
    {
        $form = $this->createForm(CreateTaskType::class);
        $form->submit($request->request->all());

        if ( ! $form->isSubmitted() || ! $form->isValid()) {
            return $form;
        }

        $createTaskDto = $this->createTaskDtoFactory->createFromArray(
            array_merge($form->getData(), ['user' => $this->getUser()])
        );

        $task = $this->createTaskService->createTask($createTaskDto);

        return $task;
    }
}