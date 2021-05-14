<?php

declare(strict_types=1);

namespace App\Service\Task;

use App\Dto\Api\Task\GenerateTasksReportDto;
use App\Entity\Task\TasksReport;
use App\Factory\Api\Task\Dto\TasksReportDataDtoFactory;
use App\Factory\Api\Task\File\TaskReportServiceFactory;
use App\Manager\DoctrineManager;
use App\Repository\Task\TaskRepository;
use Ramsey\Uuid\Uuid;

/**
 * Class ReportTasksService.
 */
class ReportTasksService
{
    private TaskReportServiceFactory $taskReportServiceFactory;

    private TasksReportDataDtoFactory $tasksReportDataDtoFactory;

    private TaskRepository $taskRepository;

    private DoctrineManager $manager;

    /**
     * ReportTasksService constructor.
     */
    public function __construct(
        TaskReportServiceFactory $taskReportServiceFactory,
        TasksReportDataDtoFactory $tasksReportDataDtoFactory,
        TaskRepository $taskRepository,
        DoctrineManager $manager
    ) {
        $this->taskReportServiceFactory = $taskReportServiceFactory;
        $this->tasksReportDataDtoFactory = $tasksReportDataDtoFactory;
        $this->taskRepository = $taskRepository;
        $this->manager = $manager;
    }

    /**
     * @throws \App\Exception\Factory\UnsupportedFactoryObject
     */
    public function generateReport(GenerateTasksReportDto $generateTasksReportDto): TasksReport
    {
        $reportWriter = $this->taskReportServiceFactory->create($generateTasksReportDto->getFormat());

        $tasksStatistic = $this->taskRepository->getStatisticsByUserAndDateRange(
            $generateTasksReportDto->getUser(),
            $generateTasksReportDto->getStartDate(),
            $generateTasksReportDto->getEndDate()
        );

        $reportFileName = $this->generateFileName();
        $tasksReportDataDto = $this->tasksReportDataDtoFactory->createFromArray(
            [
                'report_file_name' => $reportFileName,
                'tasks' => $this->taskRepository->findByUserAndDateRange(
                    $generateTasksReportDto->getUser(),
                    $generateTasksReportDto->getStartDate(),
                    $generateTasksReportDto->getEndDate()
                ),
                'total_tasks' => $tasksStatistic[TaskRepository::TOTAL_ALIAS],
                'total_time' => (int) $tasksStatistic[TaskRepository::TOTAL_TIME_SPENT_ALIAS],
            ]
        );

        $reportPath = $reportWriter->generate($tasksReportDataDto);

        $tasksReport = (new TasksReport())->setStorage(TasksReport::STORAGE_FILE)
            ->setStorageType(TasksReport::STORAGE_TYPE_LOCAL)
            ->setStorageName(\basename($reportPath))
            ->setStorageFullPath($reportPath)
            ->setUser($generateTasksReportDto->getUser())
            ->setReportOptions([
                'start_date' => $generateTasksReportDto->getStartDate(),
                'end_date' => $generateTasksReportDto->getEndDate(),
            ])
        ;
        $this->manager->save($tasksReport);

        return $tasksReport;
    }

    protected function generateFileName(): string
    {
        return Uuid::uuid4()->toString();
    }
}
