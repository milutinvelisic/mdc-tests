<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ImportRepository
{
    public function getRows(string $table, array $columns, ?string $search = null): Collection
    {
        $q = DB::table($table);
        if ($search) {
            $q->where(function($sub) use ($columns, $search) {
                foreach ($columns as $col) {
                    $sub->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        return $q->get();
    }
}
