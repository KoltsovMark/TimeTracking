<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Task\File;

use App\Entity\Task;
use App\Factory\Api\Task\Dto\TasksReportDataDtoFactory;
use App\Service\File\CsvWriterService;
use App\Service\Task\File\TaskReportCsvService;
use App\Tests\Unit\Traits\AccessiblePrivatePropertyTrait;
use Ramsey\Uuid\Uuid;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TaskReportCsvServiceTest.
 */
class TaskReportCsvServiceTest extends KernelTestCase
{
    use AccessiblePrivatePropertyTrait;

    private TasksReportDataDtoFactory $tasksReportDataDtoFactory;
    /**
     * @var CsvWriterService|\PHPUnit\Framework\MockObject\MockObject
     */
    private CsvWriterService $csvWriterServiceMock;

    /**
     * @covers \App\Service\Task\File\TaskReportCsvService::generate
     *
     * @dataProvider dataProviderForGenerate
     */
    public function testGenerate(
        array $tasks,
        int $totalTasks,
        int $totalTime,
        string $fileName
    ) {
        $tasksReportDataDto = $this->tasksReportDataDtoFactory->createFromArray([
            'tasks' => $tasks,
            'total_tasks' => $totalTasks,
            'total_time' => $totalTime,
            'report_file_name' => $fileName,
        ]);
        $expectedPreparedData = [];
        $expectedPath = 'reports/tasks/csv';
        $expectedFullPath = "{$expectedPath}/{$fileName}.csv";

        $partialMock = $this->getTaskReportCsvServicePartialMock(['prepareTaskReportData', 'getWriterService', 'getPath']);

        $partialMock->expects($this->once())
            ->method('prepareTaskReportData')
            ->with(...[$tasksReportDataDto])
            ->willReturn($expectedPreparedData)
        ;

        $partialMock->expects($this->once())
            ->method('getWriterService')
            ->willReturn($this->csvWriterServiceMock)
        ;

        $partialMock->expects($this->once())
            ->method('getPath')
            ->willReturn($expectedPath)
        ;

        $this->csvWriterServiceMock
            ->expects($this->once())
            ->method('write')
            ->with(...[$fileName, $expectedPreparedData, $expectedPath])
            ->willReturn($expectedFullPath)
        ;

        $fullPath = $partialMock->generate($tasksReportDataDto);

        $this->assertEquals($expectedFullPath, $fullPath);
    }

    /**
     * @return array[]
     */
    public function dataProviderForGenerate(): array
    {
        return [
            [
                'tasks' => [new Task(), new Task()],
                'total_tasks' => 2,
                'total_time' => 20,
                'file_name' => Uuid::uuid4()->toString(),
            ],
        ];
    }

    /**
     * @covers \App\Service\Task\File\TaskReportCsvService::getWriterService
     */
    public function testGetWriterService()
    {
        $writerService = self::$container->get(TaskReportCsvService::class)->getWriterService();

        $this->assertEquals(get_class($writerService), CsvWriterService::class);
    }

    /**
     * @covers \App\Service\Task\File\TaskReportCsvService::getPath
     */
    public function testGetPath()
    {
        $expectedPath = 'reports/tasks/csv';

        $reflectionMethod = new ReflectionMethod(TaskReportCsvService::class, 'getPath');
        $reflectionMethod->setAccessible(true);
        $path = $reflectionMethod->invoke(self::$container->get(TaskReportCsvService::class));

        $this->assertEquals($expectedPath, $path);
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->tasksReportDataDtoFactory = new TasksReportDataDtoFactory();

        $this->csvWriterServiceMock = $this->createMock(CsvWriterService::class);
    }

    protected function getTaskReportCsvServicePartialMock(array $methods = []): TaskReportCsvService
    {
        return $this->createPartialMock(TaskReportCsvService::class, $methods);
    }
}
