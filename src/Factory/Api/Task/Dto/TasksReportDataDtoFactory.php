<?php

declare(strict_types=1);

namespace App\Factory\Api\Task\Dto;

use App\Dto\Api\Task\GenerateTasksReportDto;
use App\Dto\Api\Task\TasksReportDataDto;

/**
 * Class TasksReportDataDtoFactory
 * @package App\Factory\Api\Task\Dto
 */
class TasksReportDataDtoFactory
{
    /**
     * @return TasksReportDataDto
     */
    public function createEmpty(): TasksReportDataDto
    {
        return new TasksReportDataDto();
    }

    /**
     * @param array $params
     *
     * @return TasksReportDataDto
     */
    public function createFromArray(array $params): TasksReportDataDto
    {
        return $this->createEmpty()
            ->setTasks($params['tasks'])
            ->setTotalTasks($params['total_tasks'])
            ->setTotalTime($params['total_time'])
            ->setReportFileName($params['report_file_name'])
            ;
    }
}