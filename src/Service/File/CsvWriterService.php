<?php

declare(strict_types=1);

namespace App\Service\File;

use App\Exception\File\UnsupportedDataType;
use ParseCsv\Csv;

/**
 * Class CsvWriterService.
 */
class CsvWriterService extends AbstractWriterService
{
    protected string $extension = 'csv';

    protected Csv $csvExtension;

    /**
     * CsvWriterService constructor.
     */
    public function __construct(string $projectDir)
    {
        parent::__construct($projectDir);

        $this->csvExtension = new Csv();
    }

    /**
     * @param $data
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
        if (!is_array($data)) {
            throw new UnsupportedDataType();
        }
    }
}
