<?php

declare(strict_types=1);

namespace App\Controller\Api\Task;

use App\Controller\Api\BaseController;
use App\Repository\TaskRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends BaseController
{
    private TaskRepository $taskRepository;
    private PaginatorInterface $paginator;

    public function __construct(TaskRepository $taskRepository, PaginatorInterface $paginator)
    {
        $this->taskRepository = $taskRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("tasks", name="tasks_list", methods={"GET"})
     * @Security("is_granted('ROLE_TASKS_VIEWER')")
     *
     * @Rest\View(statusCode=200)
     */
    public function index(Request $request)
    {
        //@todo add parameters validation, e.g. limit available types
        return $this->paginator->paginate(
            $this->taskRepository->findQueryByUser($this->getUser()),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );
    }
}
