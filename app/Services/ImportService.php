<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImportService
{
    protected $import;
    protected $importId;
    protected $importType;
    protected $fileKey;
    protected $configFile;
    protected $tableName;
    protected $userId;

    public function __construct(array $context)
    {
        // $context must contain import (db import record) or basic fields
        $this->import = $context['import'] ?? null;
        $this->importId = $this->import->id ?? ($context['import_id'] ?? null);
        $this->importType = $context['import_type'] ?? ($this->import->import_type ?? null);
        $this->fileKey = $context['file_key'] ?? ($this->import->file_key ?? null);
        $this->userId = $context['user_id'] ?? ($this->import->user_id ?? null);
        $this->configFile = config("imports.{$this->importType}.files.{$this->fileKey}") ?? null;
        $this->tableName = "{$this->importType}_{$this->fileKey}";
    }

    /**
     * Process rows (array of associative rows) one by one.
     * $rows: array of arrays where keys are incoming file headers or keys mapped already to config keys.
     * We expect rows keys to match config keys (e.g. 'order_date','channel', etc.)
     */
    public function processRows(array $rows): int
    {
        $errors = 0;
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $mapped = [];
            $validationFailed = false;

            foreach ($this->configFile['headers_to_db'] as $key => $meta) {
                $label = $meta['label'] ?? $key;
                $value = $row[$key] ?? null;

                $value = $this->castValue($value, $meta['type'] ?? null);

                $validationMessage = $this->validateField($key, $value, $meta);

                if ($validationMessage !== true) {
                    $this->logError($rowNumber, $key, $value, $validationMessage);
                    $validationFailed = true;
                } else {
                    $mapped[$key] = $value;
                }
            }

            if ($validationFailed) {
                $errors++;
                continue;
            }

            try {
                $this->insertOrUpdateRow($mapped);
            } catch (Throwable $e) {
                Log::error("ImportEngine failed to insert row: " . $e->getMessage());
                $this->logError($rowNumber, null, json_encode($mapped), 'Exception: '.$e->getMessage());
                $errors++;
            }
        }

        return $errors;
    }

    protected function castValue($value, $type)
    {
        if ($value === null || $value === '') return null;

        switch ($type) {
            case 'date':
                try {
                    $d = Carbon::parse($value);
                    return $d->toDateString();
                } catch (Exception $e) {
                    return $value;
                }
            case 'double':
            case 'float':
                return is_numeric($value) ? (float)$value : $value;
            case 'int':
            case 'integer':
                return is_numeric($value) ? (int)$value : $value;
            case 'string':
            default:
                return (string)$value;
        }
    }

    protected function validateField($key, $value, $meta): true|string
    {
        $rules = $meta['validation'] ?? [];
        foreach ($rules as $rule) {
            if ($rule === 'required') {
                if ($value === null || $value === '') return "Field {$meta['label']} is required";
            }

            if ($rule === 'nullable') {
                // always ok
            }

            if ($rule === 'in') {
                $allowed = $meta['in_values'] ?? [];
                if (!in_array($value, $allowed)) {
                    return "Field {$meta['label']} must be one of: ".implode(',', $allowed);
                }
            }

            if ($rule === 'exists') {
                $existsIn = $meta['exists_in'] ?? null;
                if (!$existsIn || empty($existsIn['table']) || empty($existsIn['column'])) {
                    return "Internal config error for exists rule on {$key}";
                }
                $found = DB::table($existsIn['table'])->where($existsIn['column'], $value)->exists();
                if (!$found) return "Value '{$value}' for {$meta['label']} does not exist in {$existsIn['table']}.{$existsIn['column']}";
            }

            if ($rule === 'unique') {
                // Unique within the target import table
                $exists = DB::table($this->tableName)->where($key, $value)->exists();
                if ($exists) return "Value '{$value}' for {$meta['label']} must be unique in {$this->tableName}";
            }
        }

        return true;
    }

    protected function insertOrUpdateRow(array $mapped): void
    {
        $keys = $this->configFile['update_or_create'] ?? null;

        if ($keys && is_array($keys) && count($keys)) {
            $where = [];
            foreach ($keys as $k) {
                $where[$k] = $mapped[$k] ?? null;
            }

            // find existing record
            $existing = DB::table($this->tableName)->where($where)->first();

            if ($existing) {
                // detect changes
                $updates = [];
                foreach ($mapped as $col => $val) {
                    $old = $existing->{$col} ?? null;
                    if ((string)$old !== (string)$val) {
                        $updates[$col] = $val;
                        $this->logAudit($existing->id ?? null, $col, $old, $val);
                    }
                }

                if (!empty($updates)) {
                    DB::table($this->tableName)->where('id', $existing->id)->update($updates);
                }

            } else {
                // create
                $insertId = DB::table($this->tableName)->insertGetId($mapped);
                // optionally audit creation? We skip per-row create audits for brevity
            }
        } else {
            // no keys provided -> simple insert
            DB::table($this->tableName)->insert($mapped);
        }
        return;
    }

    protected function logError($rowNumber, $columnKey, $value, $message): void
    {
        DB::table('import_errors')->insert([
            'import_id' => $this->importId,
            'file_key' => $this->fileKey,
            'row_number' => $rowNumber,
            'column_key' => $columnKey,
            'value' => is_array($value) ? json_encode($value) : (string)($value ?? ''),
            'message' => $message,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    protected function logAudit($rowId, $columnKey, $old, $new): void
    {
        DB::table('import_audits')->insert([
            'import_id' => $this->importId,
            'table_name' => $this->tableName,
            'row_id' => $rowId,
            'column_key' => $columnKey,
            'old_value' => (string)$old,
            'new_value' => (string)$new,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
