<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Task\File;

use App\Entity\Task;
use App\Factory\Api\Task\Dto\TasksReportDataDtoFactory;
use App\Service\File\PdfWriterService;
use App\Service\Task\File\TaskReportPdfService;
use App\Tests\Unit\Traits\AccessiblePrivatePropertyTrait;
use Ramsey\Uuid\Uuid;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TaskReportPdfServiceTest.
 */
class TaskReportPdfServiceTest extends KernelTestCase
{
    use AccessiblePrivatePropertyTrait;

    private TasksReportDataDtoFactory $tasksReportDataDtoFactory;
    /**
     * @var PdfWriterService|\PHPUnit\Framework\MockObject\MockObject
     */
    private PdfWriterService $pdfWriterServiceMock;

    /**
     * @covers \App\Service\Task\File\TaskReportPdfService::generate
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
        $expectedPreparedData = '<html></html>>';
        $expectedPath = 'reports/tasks/pdf';
        $expectedFullPath = "{$expectedPath}/{$fileName}.pdf";

        $partialMock = $this->getTaskReportPdfServicePartialMock(['prepareTaskReportData', 'getWriterService', 'getPath']);

        $partialMock->expects($this->once())
            ->method('prepareTaskReportData')
            ->with(...[$tasksReportDataDto])
            ->willReturn($expectedPreparedData)
        ;

        $partialMock->expects($this->once())
            ->method('getWriterService')
            ->willReturn($this->pdfWriterServiceMock)
        ;

        $partialMock->expects($this->once())
            ->method('getPath')
            ->willReturn($expectedPath)
        ;

        $this->pdfWriterServiceMock
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
     * @covers \App\Service\Task\File\TaskReportPdfService::getWriterService
     */
    public function testGetWriterService()
    {
        $writerService = self::$container->get(TaskReportPdfService::class)->getWriterService();

        $this->assertEquals(get_class($writerService), PdfWriterService::class);
    }

    /**
     * @covers \App\Service\Task\File\TaskReportPdfService::getPath
     */
    public function testGetPath()
    {
        $expectedPath = 'reports/tasks/pdf';

        $reflectionMethod = new ReflectionMethod(TaskReportPdfService::class, 'getPath');
        $reflectionMethod->setAccessible(true);
        $path = $reflectionMethod->invoke(self::$container->get(TaskReportPdfService::class));

        $this->assertEquals($expectedPath, $path);
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->tasksReportDataDtoFactory = new TasksReportDataDtoFactory();

        $this->pdfWriterServiceMock = $this->createMock(PdfWriterService::class);
    }

    protected function getTaskReportPdfServicePartialMock(array $methods = []): TaskReportPdfService
    {
        return $this->createPartialMock(TaskReportPdfService::class, $methods);
    }
}
