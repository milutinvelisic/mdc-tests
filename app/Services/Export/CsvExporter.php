<?php

namespace App\Services\Export;

use Illuminate\Support\Collection;

class CsvExporter
{
    public function export(Collection $rows, array $columns, $outputHandle = 'php://output'): void
    {
        $out = fopen($outputHandle,'w');
        fputcsv($out, $columns);

        foreach ($rows as $row) {
            $line = [];
            foreach ($columns as $c) $line[] = $row->{$c} ?? null;
            fputcsv($out, $line);
        }

        fclose($out);
    }
}
