<?php

declare(strict_types=1);

namespace App\Service\File;

use App\Exception\File\UnsupportedDataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class XlsxWriterService
 * @package App\Service\File
 */
class XlsxWriterService extends AbstractWriterService
{
    protected string $extension = 'xlsx';

    public function write(string $fileName, $data, string $path = null): void
    {
        $this->validate($data);

        $fullPath = $this->getFullPath($fileName, $path);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($data);

        $writer = new Xlsx($spreadsheet);
        $writer->save($fullPath);
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