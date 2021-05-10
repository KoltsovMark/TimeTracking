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
    /**
     * @var string
     */
    protected string $extension = 'xlsx';

    /**
     * @param string $fileName
     * @param $data
     * @param string|null $path
     *
     * @return string
     * @throws UnsupportedDataType
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function write(string $fileName, $data, string $path = null): string
    {
        $this->validate($data);

        $fullPath = $this->getFullPath($fileName, $path);
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($data);

        $writer = $this->getXlsx($spreadsheet);
        $writer->save($fullPath);

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

    /**
     * @return Spreadsheet
     */
    protected function getSpreadsheet(): Spreadsheet
    {
        return new Spreadsheet();
    }

    /**
     * @param Spreadsheet $spreadsheet
     *
     * @return Xlsx
     */
    protected function getXlsx(Spreadsheet $spreadsheet): Xlsx
    {
        return new Xlsx($spreadsheet);
    }
}