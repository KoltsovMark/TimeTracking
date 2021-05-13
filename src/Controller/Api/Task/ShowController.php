<?php

declare(strict_types=1);

namespace App\Controller\Api\Task;

use App\Controller\Api\BaseController;
use App\Entity\Task;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends BaseController
{
    /**
     * @Route("tasks/{id}", name="tasks_show", requirements={"id"="\d+"}, methods={"GET"})
     *
     * @Security("is_granted('ROLE_TASKS_VIEWER')")
     *
     * @Rest\View(statusCode=200)
     */
    public function show(Task $task)
    {
        //@todo add voter
        if ($task->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $task;
    }
}
