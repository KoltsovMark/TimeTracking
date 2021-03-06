<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\File;

use App\Exception\File\UnsupportedDataType;
use App\Service\File\CsvWriterService;
use App\Tests\Unit\Traits\AccessiblePrivatePropertyTrait;
use ParseCsv\Csv;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CsvWriterServiceTest extends KernelTestCase
{
    use AccessiblePrivatePropertyTrait;

    private Csv $csvExtensionMock;
    private string $projectPublicDirectory;

    /**
     * @covers \App\Service\File\CsvWriterService::write
     *
     * @dataProvider dataProviderForWrite
     */
    public function testWrite(string $fileName, array $data, string $expectedPath)
    {
        $partialMock = $this->getCsvWriterServicePartialMock(['validate', 'getFullPath', 'createDirectoryIfDoNotExist']);
        $expectedFullPath = \implode(DIRECTORY_SEPARATOR, [
            $this->projectPublicDirectory,
            $expectedPath,
            "{$fileName}.csv",
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

        $this->csvExtensionMock
            ->expects($this->once())
            ->method('save')
            ->with(...[$expectedFullPath, $data])
        ;

        $path = $partialMock->write($fileName, $data, $expectedPath);

        $this->assertEquals($expectedFullPath, $path);
    }

    public function dataProviderForWrite(): array
    {
        return [
            [
                'file_name' => Uuid::uuid4()->toString(),
                'data' => [],
                'expected path' => 'somePath',
            ],
        ];
    }

    /**
     * @covers \App\Service\File\CsvWriterService::validate
     */
    public function testValidate()
    {
        $data = [];

        self::$container->get(CsvWriterService::class)->validate($data);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @covers \App\Service\File\CsvWriterService::validate
     */
    public function testValidateException()
    {
        $data = 'wrongDataFormat';

        $this->expectException(UnsupportedDataType::class);

        self::$container->get(CsvWriterService::class)->validate($data);
    }

    /**
     * @covers \App\Service\File\CsvWriterService::getExtension
     */
    public function testGetExtension()
    {
        $extension = self::$container->get(CsvWriterService::class)->getExtension();

        $this->assertEquals('csv', $extension);
    }

    /**
     * @covers \App\Service\File\CsvWriterService::getFullPath
     *
     * @dataProvider dataProviderForGetFullPath
     */
    public function testGetFullPath(string $fileName, string $expectedPath)
    {
        $expectedFullPath = \implode(DIRECTORY_SEPARATOR, [
            $this->projectPublicDirectory,
            $expectedPath,
            "{$fileName}.csv",
        ]);
        $fullPath = self::$container->get(CsvWriterService::class)->getFullPath($fileName, $expectedPath);

        $this->assertEquals($expectedFullPath, $fullPath);
    }

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

        $this->csvExtensionMock = $this->createMock(Csv::class);
    }

    protected function getCsvWriterServicePartialMock(array $methods = []): CsvWriterService
    {
        $partialMock = $this->createPartialMock(CsvWriterService::class, $methods);

        $this->setPrivateProperty(
            $partialMock,
            CsvWriterService::class,
            'csvExtension',
            $this->csvExtensionMock
        );

        return $partialMock;
    }
}
