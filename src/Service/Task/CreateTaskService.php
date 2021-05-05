<?php

namespace App\Service\Task;

use App\Dto\Api\Task\CreateTaskDto;
use App\Entity\Task;
use App\Manager\DoctrineManager;

class CreateTaskService
{
    private DoctrineManager $manager;

    public function __construct(DoctrineManager $manager)
    {
        $this->manager = $manager;
    }

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