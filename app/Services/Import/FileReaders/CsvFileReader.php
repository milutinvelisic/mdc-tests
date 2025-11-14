<?php

namespace App\Services\Import\FileReaders;

use App\Interfaces\ImportFileReaderInterface;

class CsvFileReader implements ImportFileReaderInterface
{
    public function __construct(public string $path) {}

    public function read(): array
    {
        if (!file_exists($this->path)) return [];

        $rows = [];
        $headers = null;

        if (($handle = fopen($this->path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                if (!$headers) {
                    $headers = array_map('trim', $data);
                    continue;
                }

                $rows[] = array_combine($headers, $data);
            }
            fclose($handle);
        }

        return $rows;
    }
}

