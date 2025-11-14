<?php

namespace App\Services\Import;

use App\Models\Import;

class RowNormalizer
{
    protected array $labelMap;

    public function __construct(protected Import $import)
    {
        $config = config("imports.{$import->import_type}.files.{$import->file_key}") ?? [];

        $this->labelMap = collect($config['headers_to_db'] ?? [])
            ->mapWithKeys(fn($meta, $key) => [
                strtolower($meta['label'] ?? $key) => $key,
                strtolower($key) => $key,
            ])
            ->toArray();
    }

    public function apply(array $rows): array
    {
        return array_map(fn($row) => $this->normalizeRow($row), $rows);
    }

    protected function normalizeRow(array $row): array
    {
        $normalized = [];

        foreach ($row as $header => $value) {
            $key = strtolower(trim($header));
            $mapped = $this->labelMap[$key] ?? $this->sanitize($key);
            $normalized[$mapped] = $value;
        }

        return $normalized;
    }

    protected function sanitize(string $key): string
    {
        return preg_replace('/[^a-z0-9_]/', '', str_replace(' ', '_', $key));
    }
}

