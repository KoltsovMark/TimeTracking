<?php

declare(strict_types=1);

namespace App\Factory\Api\Task\Dto;

use App\Dto\Api\Task\GenerateTasksReportDto;

/**
 * Class GenerateTasksReportDtoFactory.
 */
class GenerateTasksReportDtoFactory
{
    public function createEmpty(): GenerateTasksReportDto
    {
        return new GenerateTasksReportDto();
    }

    public function createFromArray(array $params): GenerateTasksReportDto
    {
        return $this->createEmpty()
            ->setStartDate($params['start_date'])
            ->setEndDate($params['end_date'])
            ->setFormat($params['format'])
            ->setUser($params['user'])
            ;
    }
}
