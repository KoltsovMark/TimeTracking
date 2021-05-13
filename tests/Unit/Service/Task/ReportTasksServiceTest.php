<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Task;

use App\Dto\Api\Task\GenerateTasksReportDto;
use App\Dto\Api\Task\TasksReportDataDto;
use App\Entity\User;
use App\Factory\Api\Task\Dto\TasksReportDataDtoFactory;
use App\Factory\Api\Task\File\TaskReportServiceFactory;
use App\Repository\TaskRepository;
use App\Service\Task\File\TaskReportPdfService;
use App\Service\Task\ReportTasksService;
use App\Tests\Unit\Traits\AccessiblePrivatePropertyTrait;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Class ReportTasksServiceTest.
 */
class ReportTasksServiceTest extends TestCase
{
    use AccessiblePrivatePropertyTrait;

    /**
     * @var TaskReportServiceFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected TaskReportServiceFactory $taskReportServiceFactoryMock;
    /**
     * @var TasksReportDataDtoFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected TasksReportDataDtoFactory $tasksReportDataDtoFactoryMock;
    /**
     * @var TaskReportPdfService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected TaskReportPdfService $taskReportPdfServiceMock;
    /**
     * @var TaskRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected TaskRepository $taskRepositoryMock;

    /**
     * @covers \App\Service\Task\ReportTasksService::generateReport
     *
     * @dataProvider dataProviderForGenerateReport
     */
    public function testGenerateReport(DateTime $startDate, DateTime $endDate, string $format, User $user)
    {
        $reportTasksServicePartialMock = $this->getReportTasksServicePartialMock(['generateFileName']);
        $tasksStatistic = [
            'total' => 10,
            'total_time_spent' => 10000,
        ];
        $fileName = 'tmp_name';
        $filePath = 'reports/task/'.$fileName;
        $tasks = [];
        $tasksReportDataDto = new TasksReportDataDto();

        $this->taskReportServiceFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with(...[$format])
            ->willReturn($this->taskReportPdfServiceMock)
        ;

        $this->taskRepositoryMock
            ->expects($this->once())
            ->method('getStatisticsByUserAndDateRange')
            ->with(...[$user, $startDate, $endDate])
            ->willReturn($tasksStatistic)
        ;

        $reportTasksServicePartialMock
            ->expects($this->once())
            ->method('generateFileName')
            ->willReturn($fileName)
        ;

        $this->taskRepositoryMock
            ->expects($this->once())
            ->method('findByUserAndDateRange')
            ->with(...[$user, $startDate, $endDate])
            ->willReturn($tasks)
        ;

        $this->tasksReportDataDtoFactoryMock
            ->expects($this->once())
            ->method('createFromArray')
            ->with([
                'report_file_name' => $fileName,
                'tasks' => $tasks,
                'total_tasks' => $tasksStatistic['total'],
                'total_time' => $tasksStatistic['total_time_spent'],
            ])
            ->willReturn($tasksReportDataDto)
        ;

        $this->taskReportPdfServiceMock
            ->expects($this->once())
            ->method('generate')
            ->with(...[$tasksReportDataDto])
            ->willReturn($filePath)
        ;

        $generateTasksReportDto = (new GenerateTasksReportDto())->setStartDate($startDate)
            ->setEndDate($endDate)
            ->setFormat($format)
            ->setUser($user)
        ;
        $result = $reportTasksServicePartialMock->generateReport($generateTasksReportDto);

        $this->assertIsString($result);
        $this->assertEquals($filePath, $result);
    }

    /**
     * @return array[]
     */
    public function dataProviderForGenerateReport(): array
    {
        return [
            [
                (new DateTime())->modify('-1 day'),
                new DateTime(),
                'pdf',
                new User(),
            ],
            [
                (new DateTime())->modify('-10 days'),
                (new DateTime())->modify('-1 day'),
                'csv',
                new User(),
            ],
            [
                (new DateTime())->modify('-1 day'),
                (new DateTime())->modify('+1 day'),
                'excel',
                new User(),
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskReportServiceFactoryMock = $this->createMock(TaskReportServiceFactory::class);
        $this->tasksReportDataDtoFactoryMock = $this->createMock(TasksReportDataDtoFactory::class);
        $this->taskRepositoryMock = $this->createMock(TaskRepository::class);
        $this->taskReportPdfServiceMock = $this->createMock(TaskReportPdfService::class);
    }

    /**
     * @return ReportTasksService|\PHPUnit\Framework\MockObject\MockObject
     *
     * @throws \ReflectionException
     */
    protected function getReportTasksServicePartialMock(array $methods = []): ReportTasksService
    {
        $reportTasksServicePartialMock = $this->createPartialMock(ReportTasksService::class, $methods);

        $this->setPrivateProperty(
            $reportTasksServicePartialMock,
            ReportTasksService::class,
            'taskReportServiceFactory',
            $this->taskReportServiceFactoryMock
        );
        $this->setPrivateProperty(
            $reportTasksServicePartialMock,
            ReportTasksService::class,
            'tasksReportDataDtoFactory',
            $this->tasksReportDataDtoFactoryMock
        );
        $this->setPrivateProperty(
            $reportTasksServicePartialMock,
            ReportTasksService::class,
            'taskRepository',
            $this->taskRepositoryMock
        );

        return $reportTasksServicePartialMock;
    }
}
