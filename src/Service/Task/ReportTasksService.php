<?php

declare(strict_types=1);

namespace App\Service\Task;

use App\Dto\Api\Task\GenerateTasksReportDto;
use App\Factory\Api\Task\Dto\TasksReportDataDtoFactory;
use App\Factory\Api\Task\File\TaskReportServiceFactory;
use App\Repository\TaskRepository;
use Ramsey\Uuid\Uuid;

/**
 * Class ReportTasksService
 * @package App\Service\Task
 */
class ReportTasksService
{
    /**
     * @var TaskReportServiceFactory
     */
    private TaskReportServiceFactory $taskReportServiceFactory;
    /**
     * @var TasksReportDataDtoFactory
     */
    private TasksReportDataDtoFactory $tasksReportDataDtoFactory;
    /**
     * @var TaskRepository
     */
    private TaskRepository $taskRepository;

    /**
     * ReportTasksService constructor.
     *
     * @param TaskReportServiceFactory $taskReportServiceFactory
     * @param TasksReportDataDtoFactory $tasksReportDataDtoFactory
     * @param TaskRepository $taskRepository
     */
    public function __construct(
        TaskReportServiceFactory $taskReportServiceFactory,
        TasksReportDataDtoFactory $tasksReportDataDtoFactory,
        TaskRepository $taskRepository
    ) {
        $this->taskReportServiceFactory = $taskReportServiceFactory;
        $this->tasksReportDataDtoFactory = $tasksReportDataDtoFactory;
        $this->taskRepository = $taskRepository;
    }

    /**
     * @param GenerateTasksReportDto $generateTasksReportDto
     *
     * @return string
     * @throws \App\Exception\Factory\UnsupportedFactoryObject
     */
    public function generateReport(GenerateTasksReportDto $generateTasksReportDto): string
    {
        //@todo add events to service
        $reportWriter = $this->taskReportServiceFactory->create($generateTasksReportDto->getFormat());

        $tasksStatistic = $this->taskRepository->getStatisticsByUserAndDateRange(
            $generateTasksReportDto->getUser(),
            $generateTasksReportDto->getStartDate(),
            $generateTasksReportDto->getEndDate()
        );

        $tasksReportDataDto = $this->tasksReportDataDtoFactory->createFromArray(
            [
                'report_file_name' => $this->generateFileName(),
                'tasks' => $this->taskRepository->findByUserAndDateRange(
                    $generateTasksReportDto->getUser(),
                    $generateTasksReportDto->getStartDate(),
                    $generateTasksReportDto->getEndDate()
                ),
                'total_tasks' => $tasksStatistic[TaskRepository::TOTAL_ALIAS],
                'total_time' => (int) $tasksStatistic[TaskRepository::TOTAL_TIME_SPENT_ALIAS],
            ]
        );

        //@todo add generation by chunks when it possible
        return $reportWriter->generate($tasksReportDataDto);
    }

    /**
     * @return string
     */
    protected function generateFileName(): string
    {
        return Uuid::uuid4()->toString();
    }
}