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
     * @param string $fileName
     * @param $data
     * @param string|null $path
     *
     * @throws UnsupportedDataType
     */
    public function write(string $fileName, $data, string $path = null): void
    {
        $this->validate($data);

        $fullPath = $this->getFullPath($fileName, $path);
        (new Csv())->save($fullPath, $data);
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