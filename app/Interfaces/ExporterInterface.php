<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
interface ExporterInterface
{
    /**
     * Export data as a downloadable stream.
     *
     * @param array $columns Column headers
     * @param array|Collection $rows Array or collection of rows
     * @param string $filename
     * @return StreamedResponse
     */
    public function toStream(array $columns, array|Collection $rows, string $filename): StreamedResponse;

    public function getExtension(): string;
}
