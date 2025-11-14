<?php

namespace App\Services\Import;

use App\Models\Import;
use App\Services\Import\FileReaders\CsvFileReader;
use App\Services\Import\FileReaders\ExcelFileReader;
use App\Services\ImportService;
use Exception;

class ImportRunner
{
    protected Import $import;

    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    /**
     * @throws Exception
     */
    public function run(): int
    {
        $reader = $this->resolveReader();

        $rows = $reader->read();

        $normalizer = new RowNormalizer($this->import);
        $normalizedRows = $normalizer->apply($rows);

        $engine = new ImportService([
            'import' => $this->import,
            'import_id' => $this->import->id,
            'import_type' => $this->import->import_type,
            'file_key' => $this->import->file_key,
            'user_id' => $this->import->user_id,
        ]);

        return $engine->processRows($normalizedRows);
    }

    /**
     * @throws Exception
     */
    protected function resolveReader(): CsvFileReader|ExcelFileReader
    {
        $path = storage_path("app/public/{$this->import->stored_file_path}");
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'csv', 'txt' => new CsvFileReader($path),
            'xls', 'xlsx' => new ExcelFileReader($path),
            default => throw new Exception("Unsupported file type: {$ext}")
        };
    }
}
