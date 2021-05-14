<?php

declare(strict_types=1);

namespace App\Service\File;

use App\Exception\File\UnsupportedDataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class XlsxWriterService.
 */
class XlsxWriterService extends AbstractWriterService
{
    protected string $extension = 'xlsx';

    /**
     * @param array $data
     *
     * @throws UnsupportedDataType
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function write(string $fileName, $data, string $path = null): string
    {
        $this->validate($data);
        $this->createDirectoryIfDoNotExist($path);

        $fullPath = $this->getFullPath($fileName, $path);
        $spreadsheet = $this->getSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($data);
        $this->setColumnsAutoSize($sheet, \count($data[0] ?? []));

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
        if (!is_array($data)) {
            throw new UnsupportedDataType();
        }
    }

    protected function getSpreadsheet(): Spreadsheet
    {
        return new Spreadsheet();
    }

    protected function getXlsx(Spreadsheet $spreadsheet): Xlsx
    {
        return new Xlsx($spreadsheet);
    }

    /**
     * Enable columns AutoSize.
     */
    protected function setColumnsAutoSize(Worksheet $sheet, int $totalColumns)
    {
        $alphabet = range('A', 'Z');

        for ($i = 0; $i < $totalColumns; ++$i) {
            $sheet->getColumnDimension($alphabet[$i])->setAutoSize(true);
        }
    }
}
