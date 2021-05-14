<?php

declare(strict_types=1);

namespace App\Controller\Api\Task;

use App\Controller\Api\BaseController;
use App\Dto\Api\Form\PaginatedPageTypeDto;
use App\Entity\Task;
use App\Form\Api\PaginatedPageType;
use App\Repository\TaskRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation as SWG;
use OpenApi\Annotations as OA;
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
     * @Rest\View(statusCode=200)
     *
     * @SWG\Security(name="Bearer")
     * @OA\Get(
     *     tags={"Tasks"},
     *     summary="Return a list of authorized user's tasks",
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref=@SWG\Model(type=PaginatedPageType::class))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of user tasks",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="page", type="int", example="1"),
     *                 @OA\Property(property="per_page", type="int", example="10"),
     *                 @OA\Property(property="total_items", type="int", example="15"),
     *                 @OA\Property(
     *                      type="array",
     *                      property="items",
     *                      @OA\Items(ref=@SWG\Model(type=Task::class))
     *                 )
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
    public function index(Request $request)
    {
        // @todo extend by own class in case of new parameters
        $form = $this->createGetForm(PaginatedPageType::class, $request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $form;
        }

        /**
         * @var PaginatedPageTypeDto $paginatedPageDto
         */
        $paginatedPageDto = $form->getData();

        /*
         * @todo move paginator to a separates service
         */
        return $this->paginator->paginate(
            $this->taskRepository->findQueryByUser($this->getUser()),
            $paginatedPageDto->getPage() ?? 1,
            $paginatedPageDto->getLimit() ?? 10,
        );
    }
}
