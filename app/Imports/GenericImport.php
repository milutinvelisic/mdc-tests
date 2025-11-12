<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class GenericImport implements ToCollection
{
    public $rows;

    public function __construct()
    {
        $this->rows = collect();
    }

    public function collection(Collection $rows): void
    {
        $this->rows = $rows;
    }
}
