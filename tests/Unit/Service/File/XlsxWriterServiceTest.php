<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\File;

use App\Exception\File\UnsupportedDataType;
use App\Service\File\PdfWriterService;
use App\Service\File\XlsxWriterService;
use App\Tests\Unit\Traits\AccessiblePrivatePropertyTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class XlsxWriterServiceTest.
 */
class XlsxWriterServiceTest extends KernelTestCase
{
    use AccessiblePrivatePropertyTrait;

    private string $projectPublicDirectory;

    /**
     * @var PdfWriterService|\PHPUnit\Framework\MockObject\MockObject
     */
    private PdfWriterService $pdfWriterService;
    /**
     * @var Spreadsheet|\PHPUnit\Framework\MockObject\MockObject
     */
    private Spreadsheet $spreadsheetMock;
    /**
     * @var Worksheet|\PHPUnit\Framework\MockObject\MockObject
     */
    private Worksheet $worksheetMock;
    /**
     * @var Xlsx|\PHPUnit\Framework\MockObject\MockObject
     */
    private Xlsx $xlsxWriterMock;

    /**
     * @covers \App\Service\File\XlsxWriterService::write
     *
     * @dataProvider dataProviderForWrite
     */
    public function testWrite(string $fileName, array $data, string $expectedPath)
    {
        $partialMock = $this->getXlsxWriterServicePartialMock([
            'validate',
            'getFullPath',
            'getSpreadsheet',
            'getXlsx',
            'createDirectoryIfDoNotExist',
        ]);
        $expectedFullPath = \implode(DIRECTORY_SEPARATOR, [
            $this->projectPublicDirectory,
            $expectedPath,
            "{$fileName}.xlsx",
        ]);

        $partialMock
            ->expects($this->once())
            ->method('validate')
            ->with(...[$data])
        ;

        $partialMock
            ->expects($this->once())
            ->method('createDirectoryIfDoNotExist')
            ->with(...[$expectedPath])
        ;

        $partialMock
            ->expects($this->once())
            ->method('getFullPath')
            ->with(...[$fileName, $expectedPath])
            ->willReturn($expectedFullPath)
        ;

        $partialMock
            ->expects($this->once())
            ->method('getSpreadsheet')
            ->willReturn($this->spreadsheetMock)
        ;

        $this->spreadsheetMock
            ->expects($this->once())
            ->method('getActiveSheet')
            ->willReturn($this->worksheetMock)
        ;

        $this->worksheetMock
            ->expects($this->once())
            ->method('fromArray')
            ->with(...[$data])
        ;

        $partialMock
            ->expects($this->once())
            ->method('getXlsx')
            ->with(...[$this->spreadsheetMock])
            ->willReturn($this->xlsxWriterMock)
        ;

        $this->xlsxWriterMock
            ->expects($this->once())
            ->method('save')
            ->with(...[$expectedFullPath])
        ;

        $path = $partialMock->write($fileName, $data, $expectedPath);

        $this->assertEquals($expectedFullPath, $path);
    }

    /**
     * @return array[]
     */
    public function dataProviderForWrite(): array
    {
        return [
            [
                'file_name' => Uuid::uuid4()->toString(),
                'data' => [],
                'expected_path' => 'somePath',
            ],
        ];
    }

    /**
     * @covers \App\Service\File\XlsxWriterService::validate
     */
    public function testValidate()
    {
        $data = [];

        self::$container->get(XlsxWriterService::class)->validate($data);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \App\Service\File\XlsxWriterService::validate
     */
    public function testValidateException()
    {
        $data = 'wrongDataFormat';

        $this->expectException(UnsupportedDataType::class);

        self::$container->get(XlsxWriterService::class)->validate($data);
    }

    /**
     * @covers \App\Service\File\XlsxWriterService::getExtension
     */
    public function testGetExtension()
    {
        $extension = self::$container->get(XlsxWriterService::class)->getExtension();

        $this->assertEquals('xlsx', $extension);
    }

    /**
     * @covers \App\Service\File\XlsxWriterService::getFullPath
     *
     * @dataProvider dataProviderForGetFullPath
     */
    public function testGetFullPath(string $fileName, string $expectedPath)
    {
        $expectedFullPath = \implode(DIRECTORY_SEPARATOR, [
            $this->projectPublicDirectory,
            $expectedPath,
            "{$fileName}.xlsx",
        ]);
        $fullPath = self::$container->get(XlsxWriterService::class)->getFullPath($fileName, $expectedPath);

        $this->assertEquals($expectedFullPath, $fullPath);
    }

    /**
     * @return array[]
     */
    public function dataProviderForGetFullPath(): array
    {
        return [
            [
                'file_name' => Uuid::uuid4()->toString(),
                'path' => 'somePath',
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->projectPublicDirectory = self::$container->getParameter('kernel.project_dir').DIRECTORY_SEPARATOR.'public';
        $this->pdfWriterService = $this->createMock(PdfWriterService::class);

        $this->spreadsheetMock = $this->createMock(Spreadsheet::class);
        $this->worksheetMock = $this->createMock(Worksheet::class);
        $this->xlsxWriterMock = $this->createMock(Xlsx::class);
    }

    protected function getXlsxWriterServicePartialMock(array $methods = []): XlsxWriterService
    {
        return $this->createPartialMock(XlsxWriterService::class, $methods);
    }
}
