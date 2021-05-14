<?php

declare(strict_types=1);

namespace App\Factory\Api\Task\Dto;

use App\Dto\Api\Form\Task\CreateTaskTypeDto;
use App\Dto\Api\Task\CreateTaskDto;
use App\Entity\User;

class CreateTaskDtoFactory
{
    public function createEmpty(): CreateTaskDto
    {
        return new CreateTaskDto();
    }

    public function createFromCreateTaskTypeDto(CreateTaskTypeDto $createTaskTypeDto, User $user): CreateTaskDto
    {
        return $this->createEmpty()
            ->setTitle($createTaskTypeDto->getTitle())
            ->setComment($createTaskTypeDto->getComment())
            ->setDate($createTaskTypeDto->getDate())
            ->setTimeSpent($createTaskTypeDto->getTimeSpent())
            ->setUser($user)
        ;
    }
}
