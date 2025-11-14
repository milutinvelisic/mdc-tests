<?php

namespace App\Services\Import\FileReaders;

use App\Interfaces\ImportFileReaderInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelFileReader implements ImportFileReaderInterface
{
    public function __construct(public string $path) {}

    public function read(): array
    {
        if (!file_exists($this->path)) {
            return [];
        }

        $reader = IOFactory::createReaderForFile($this->path);
        $spreadsheet = $reader->load($this->path);

        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        $headers = null;
        $rows = [];

        foreach ($data as $rowArr) {
            $values = array_values($rowArr);

            if (!$headers) {
                // first row = header row
                $headers = array_map('trim', $values);
                continue;
            }

            $row = [];
            foreach ($headers as $i => $header) {
                $row[$header] = $values[$i] ?? null;
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
