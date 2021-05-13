<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Task\File;

use App\Entity\Task;
use App\Factory\Api\Task\Dto\TasksReportDataDtoFactory;
use App\Service\File\XlsxWriterService;
use App\Service\Task\File\TaskReportXlsxService;
use App\Tests\Unit\Traits\AccessiblePrivatePropertyTrait;
use Ramsey\Uuid\Uuid;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TaskReportXlsxServiceTest.
 */
class TaskReportXlsxServiceTest extends KernelTestCase
{
    use AccessiblePrivatePropertyTrait;

    private TasksReportDataDtoFactory $tasksReportDataDtoFactory;
    /**
     * @var XlsxWriterService|\PHPUnit\Framework\MockObject\MockObject
     */
    private XlsxWriterService $xlsxWriterServiceMock;

    /**
     * @covers \App\Service\Task\File\TaskReportXlsxService::generate
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
        $expectedPath = 'reports/tasks/xlsx';
        $expectedFullPath = "{$expectedPath}/{$fileName}.xlsx";

        $partialMock = $this->getTaskReportXlsxServicePartialMock(['prepareTaskReportData', 'getWriterService', 'getPath']);

        $partialMock->expects($this->once())
            ->method('prepareTaskReportData')
            ->with(...[$tasksReportDataDto])
            ->willReturn($expectedPreparedData)
        ;

        $partialMock->expects($this->once())
            ->method('getWriterService')
            ->willReturn($this->xlsxWriterServiceMock)
        ;

        $partialMock->expects($this->once())
            ->method('getPath')
            ->willReturn($expectedPath)
        ;

        $this->xlsxWriterServiceMock
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
     * @covers \App\Service\Task\File\TaskReportXlsxService::getWriterService
     */
    public function testGetWriterService()
    {
        $writerService = self::$container->get(TaskReportXlsxService::class)->getWriterService();

        $this->assertEquals(get_class($writerService), XlsxWriterService::class);
    }

    /**
     * @covers \App\Service\Task\File\TaskReportXlsxService::getPath
     */
    public function testGetPath()
    {
        $expectedPath = 'reports/tasks/excel';

        $reflectionMethod = new ReflectionMethod(TaskReportXlsxService::class, 'getPath');
        $reflectionMethod->setAccessible(true);
        $path = $reflectionMethod->invoke(self::$container->get(TaskReportXlsxService::class));

        $this->assertEquals($expectedPath, $path);
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->tasksReportDataDtoFactory = new TasksReportDataDtoFactory();

        $this->xlsxWriterServiceMock = $this->createMock(XlsxWriterService::class);
    }

    protected function getTaskReportXlsxServicePartialMock(array $methods = []): TaskReportXlsxService
    {
        return $this->createPartialMock(TaskReportXlsxService::class, $methods);
    }
}
