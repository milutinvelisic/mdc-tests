<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
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

    public function getPaginatedRows(string $table, array $columns, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = DB::table($table);

        if ($search) {
            $query->where(function ($sub) use ($columns, $search) {
                foreach ($columns as $col) {
                    $sub->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        return $query->paginate($perPage)->appends(request()->query());
    }

    public function getRowAudits(string $table, array $rowIds, array $columns): Collection
    {
        if (empty($rowIds) || empty($columns)) {
            return collect();
        }

        return DB::table('import_audits')
            ->where('table_name', $table)
            ->whereIn('row_id', $rowIds)
            ->whereIn('column_key', $columns)
            ->get()
            ->groupBy('row_id');
    }

    public function deleteRow(string $importType, string $fileKey, int $id, $user): void
    {
        $cfg = config("imports.{$importType}");
        $permission = $cfg['permission_required'] ?? null;

        if ($permission && !$user->can($permission)) {
            abort(403);
        }

        $table = "{$importType}_{$fileKey}";

        DB::table($table)->where('id', $id)->delete();
    }

    public function getAudits(string $importType, string $fileKey, int $rowId): Collection
    {
        $table = "{$importType}_{$fileKey}";

        return DB::table('import_audits')
            ->where('table_name', $table)
            ->where('row_id', $rowId)
            ->get();
    }
}
