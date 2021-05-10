<?php

declare(strict_types=1);

namespace App\Service\File;

use App\Exception\File\UnsupportedDataType;
use ParseCsv\Csv;

/**
 * Class CsvWriterService
 * @package App\Service\File
 */
class CsvWriterService extends AbstractWriterService
{
    /**
     * @var string
     */
    protected string $extension = 'csv';

    /**
     * @var Csv
     */
    protected Csv $csvExtension;

    /**
     * CsvWriterService constructor.
     */
    public function __construct()
    {
        $this->csvExtension = new Csv();
    }

    /**
     * @param string $fileName
     * @param $data
     * @param string|null $path
     *
     * @throws UnsupportedDataType
     */
    public function write(string $fileName, $data, string $path = null): string
    {
        $this->validate($data);

        $fullPath = $this->getFullPath($fileName, $path);
        $this->csvExtension->save($fullPath, $data);

        return $fullPath;
    }

    /**
     * @param $data
     *
     * @throws UnsupportedDataType
     */
    public function validate($data): void
    {
        if ( ! is_array($data)) {
            throw new UnsupportedDataType();
        }
    }
}