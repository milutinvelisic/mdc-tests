<?php

namespace App\Services\Export;

use App\Interfaces\ExporterInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter implements ExporterInterface
{
    public function getExtension(): string
    {
        return 'csv';
    }

    /**
     * Export data as CSV and return a StreamedResponse
     *
     * @param array|Collection $rows   Collection of objects or array of rows
     * @param array $columns           Column headers in order
     * @param string $filename         Name of the CSV file
     * @return StreamedResponse
     */
    public function toStream(array $columns, array|Collection $rows, string $filename = 'export.csv'): StreamedResponse
    {
        return new StreamedResponse(function () use ($rows, $columns) {
            $out = fopen('php://output', 'w');

            fputcsv($out, $columns);

            foreach ($rows as $row) {
                fputcsv($out, $row);
            }

            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}
