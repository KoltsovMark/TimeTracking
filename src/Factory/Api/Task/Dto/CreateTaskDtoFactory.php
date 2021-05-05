<?php

declare(strict_types=1);

namespace App\Factory\Api\Task\Dto;

use App\Dto\Api\Task\CreateTaskDto;

class CreateTaskDtoFactory
{
    public function createEmpty(): CreateTaskDto
    {
        return new CreateTaskDto();
    }

    public function createFromArray(array $params): CreateTaskDto
    {
        return $this->createEmpty()
            ->setTitle($params['title'])
            ->setComment($params['comment'])
            ->setDate($params['date'])
            ->setTimeSpent($params['time_spent'])
            ->setUser($params['user'])
            ;
    }
}