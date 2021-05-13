<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\File;

use App\Exception\File\UnsupportedDataType;
use App\Model\Configuration\File\PdfWriterConfiguration;
use App\Service\File\PdfWriterService;
use App\Tests\Unit\Traits\AccessiblePrivatePropertyTrait;
use Dompdf\Dompdf;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class PdfWriterServiceTest.
 */
class PdfWriterServiceTest extends KernelTestCase
{
    use AccessiblePrivatePropertyTrait;

    private string $projectPublicDirectory;

    /**
     * @var Filesystem|\PHPUnit\Framework\MockObject\MockObject
     */
    protected Filesystem $filesystemMock;
    /**
     * @var PdfWriterConfiguration|\PHPUnit\Framework\MockObject\MockObject
     */
    protected PdfWriterConfiguration $pdfWriterConfigurationMock;
    /**
     * @var Dompdf|\PHPUnit\Framework\MockObject\MockObject
     */
    protected Dompdf $dompdfMock;

    /**
     * @covers \App\Service\File\PdfWriterService::write
     *
     * @dataProvider dataProviderForWrite
     */
    public function testWrite(string $fileName, string $data, string $expectedPath)
    {
        $partialMock = $this->getPdfWriterServicePartialMock(['validate', 'getFullPath', 'getDompdf']);
        $expectedFullPath = "$this->projectPublicDirectory/{$expectedPath}/{$fileName}.csv";
        $expectedDom = '<html></html>';
        $expectedPaper = 'A4';
        $expectedOrientation = 'portrait';

        $partialMock
            ->expects($this->once())
            ->method('validate')
            ->with(...[$data])
        ;

        $partialMock
            ->expects($this->once())
            ->method('getFullPath')
            ->with(...[$fileName, $expectedPath])
            ->willReturn($expectedFullPath)
        ;

        $partialMock
            ->expects($this->once())
            ->method('getDompdf')
            ->willReturn($this->dompdfMock)
        ;

        $this->dompdfMock
            ->expects($this->once())
            ->method('loadHtml')
            ->with(...[$data])
        ;

        $this->pdfWriterConfigurationMock
            ->expects($this->once())
            ->method('getPaper')
            ->willReturn($expectedPaper)
        ;

        $this->pdfWriterConfigurationMock
            ->expects($this->once())
            ->method('getOrientation')
            ->willReturn($expectedOrientation)
        ;

        $this->dompdfMock
            ->expects($this->once())
            ->method('setPaper')
            ->with(...[$expectedPaper, $expectedOrientation])
        ;

        $this->dompdfMock
            ->expects($this->once())
            ->method('render')
        ;

        $this->dompdfMock
            ->expects($this->once())
            ->method('output')
            ->willReturn($expectedDom)
        ;

        $this->filesystemMock
            ->expects($this->once())
            ->method('dumpFile')
            ->with(...[$expectedFullPath, $expectedDom])
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
                'data' => '<html></html>',
                'expected_path' => 'somePath',
            ],
        ];
    }

    /**
     * @covers \App\Service\File\PdfWriterService::validate
     */
    public function testValidate()
    {
        $data = '<html></html>';

        self::$container->get(PdfWriterService::class)->validate($data);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \App\Service\File\PdfWriterService::validate
     */
    public function testValidateException()
    {
        $data = [];

        $this->expectException(UnsupportedDataType::class);

        self::$container->get(PdfWriterService::class)->validate($data);
    }

    /**
     * @covers \App\Service\File\PdfWriterService::getExtension
     */
    public function testGetExtension()
    {
        $extension = self::$container->get(PdfWriterService::class)->getExtension();

        $this->assertEquals('pdf', $extension);
    }

    /**
     * @covers \App\Service\File\PdfWriterService::getFullPath
     *
     * @dataProvider dataProviderForGetFullPath
     */
    public function testGetFullPath(string $fileName, string $path)
    {
        $expectedPath = "$this->projectPublicDirectory/{$path}/{$fileName}.pdf";
        $fullPath = self::$container->get(PdfWriterService::class)->getFullPath($fileName, $path);

        $this->assertEquals($expectedPath, $fullPath);
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

        $this->projectPublicDirectory = self::$container->getParameter('kernel.project_dir').'/public';

        $this->filesystemMock = $this->createMock(Filesystem::class);
        $this->pdfWriterConfigurationMock = $this->createMock(PdfWriterConfiguration::class);
        $this->dompdfMock = $this->createMock(Dompdf::class);
    }

    protected function getPdfWriterServicePartialMock(array $methods = []): PdfWriterService
    {
        $partialMock = $this->createPartialMock(PdfWriterService::class, $methods);
        $this->setPrivateProperty(
            $partialMock,
            PdfWriterService::class,
            'filesystem',
            $this->filesystemMock
        );
        $this->setPrivateProperty(
            $partialMock,
            PdfWriterService::class,
            'configuration',
            $this->pdfWriterConfigurationMock
        );

        return $partialMock;
    }
}
