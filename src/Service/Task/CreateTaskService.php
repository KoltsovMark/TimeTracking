<?php

declare(strict_types=1);

namespace App\Service\Task;

use App\Dto\Api\Task\CreateTaskDto;
use App\Entity\Task;
use App\Manager\DoctrineManager;

/**
 * Class CreateTaskService
 * @package App\Service\Task
 */
class CreateTaskService
{
    /**
     * @var DoctrineManager
     */
    private DoctrineManager $manager;

    /**
     * CreateTaskService constructor.
     *
     * @param DoctrineManager $manager
     */
    public function __construct(DoctrineManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param CreateTaskDto $createTaskDto
     *
     * @return Task
     * @throws \Exception
     */
    public function createTask(CreateTaskDto $createTaskDto): Task
    {
        $task = (new Task())->setTitle($createTaskDto->getTitle())
            ->setComment($createTaskDto->getComment())
            ->setTimeSpent($createTaskDto->getTimeSpent())
            ->setDate($createTaskDto->getDate())
            ->setUser($createTaskDto->getUser())
        ;

        return $this->manager->save($task);
    }
}