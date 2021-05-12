<?php

declare(strict_types=1);

namespace App\Service\Task;

use App\Dto\Api\Task\CreateTaskDto;
use App\Entity\Task;
use App\Manager\DoctrineManager;

/**
 * Class CreateTaskService.
 */
class CreateTaskService
{
    private DoctrineManager $manager;

    /**
     * CreateTaskService constructor.
     */
    public function __construct(DoctrineManager $manager)
    {
        $this->manager = $manager;
    }

    /**
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
