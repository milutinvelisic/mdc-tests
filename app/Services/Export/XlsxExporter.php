<?php

namespace App\Services\Export;

use App\Interfaces\ExporterInterface;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class XlsxExporter implements ExporterInterface
{
    public function getExtension(): string
    {
        return 'xlsx';
    }

    /**
     * Convert array data + headers to XLSX and return a streamed response
     *
     * @param array $columns
     * @param array|Collection $rows
     * @param string $filename
     * @return StreamedResponse
     */
    public function toStream(array $columns, array|Collection $rows, string $filename = 'export.xlsx'): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray($columns, null, 'A1');

        if (!empty($rows)) {
            $sheet->fromArray($rows, null, 'A2');
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="' . $filename . '"'
        );

        return $response;
    }
}
