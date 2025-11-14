<?php

namespace App\Jobs;

use App\Events\ImportFailed;
use App\ImportStatus;
use App\Mail\ImportFailedMail;
use App\Models\User;
use App\Notifications\ImportFinishedNotification;
use App\Services\ImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $importId;

    public function __construct(int $importId)
    {
        $this->importId = $importId;
    }

    public function handle(): void
    {
        $import = DB::table('imports')->where('id', $this->importId)->first();
        if (!$import) return;

        DB::table('imports')->where('id', $this->importId)->update(['status' => 'running', 'updated_at' => now()]);

        $filePath = storage_path('app/public/' . $import->stored_file_path);

        $rows = $this->readFileRows($filePath);

        $context = [
            'import' => $import,
            'import_id' => $import->id,
            'import_type' => $import->import_type,
            'file_key' => $import->file_key,
            'user_id' => $import->user_id
        ];

        $engine = new ImportService($context);
        $errors = $engine->processRows($rows);

        $user = User::where('id', $import->user_id)->first();

        if ($errors > 0) {
            $message = "{$errors} rows failed validation";
            DB::table('imports')->where('id', $this->importId)->update([
                'status' => 'failed',
                'message' => $message,
                'updated_at' => now()
            ]);

            ImportFailed::dispatch($import, $message);

            Notification::send(
                $user,
                new ImportFinishedNotification(
                    $import->id,
                    $import->import_type,
                    $import->file_key,
                    ImportStatus::FAILED,
                    ImportStatus::ERROR_MESSAGE
                )
            );
        } else {
            DB::table('imports')->where('id', $this->importId)->update([
                'status' => 'completed',
                'message' => 'Import completed',
                'updated_at' => now()
            ]);

            Notification::send(
                $user,
                new ImportFinishedNotification(
                    $import->id,
                    $import->import_type,
                    $import->file_key,
                    ImportStatus::COMPLETED,
                    ImportStatus::SUCCESSFUL_MESSAGE
                )
            );
        }

    }

    protected function readFileRows($path): array
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $rows = [];

        if (in_array($ext, ['csv','txt'])) {
            if (!file_exists($path)) return [];
            $handle = fopen($path, 'r');
            if ($handle === false) return [];

            $headers = null;
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                if (!$headers) {
                    $headers = array_map(function($h){ return trim($h); }, $data);
                    continue;
                }
                $row = [];
                foreach ($headers as $i => $h) {
                    $row[$h] = $data[$i] ?? null;
                }
                // convert header names to config keys if necessary (we expect config keys match headers)
                $rows[] = $this->normalizeRowKeys($row);
            }
            fclose($handle);
        } elseif (in_array($ext, ['xls','xlsx'])) {
            $reader = IOFactory::createReaderForFile($path);
            $spreadsheet = $reader->load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);

            $headers = null;
            foreach ($data as $rIndex => $rowArr) {
                // $rowArr is like ['A' => value, 'B' => value ...]
                $values = array_values($rowArr);
                if (!$headers) {
                    $headers = array_map(function($h) { return trim($h); }, $values);
                    continue;
                }
                $row = [];
                foreach ($headers as $i => $h) {
                    $row[$h] = $values[$i] ?? null;
                }
                $rows[] = $this->normalizeRowKeys($row);
            }
        } else {
            // unsupported
        }

        return $rows;
    }

    /**
     * Normalize header names to config keys:
     * - If file headers are exactly the config keys (like 'order_date'), we keep them.
     * - If headers are human labels (like 'Order Date'), we try matching by label from config.
     */
    protected function normalizeRowKeys(array $row): array
    {
        // Find config for import to know mapping
        $import = DB::table('imports')->where('id', $this->importId)->first();
        $config = config("imports.{$import->import_type}.files.{$import->file_key}") ?? null;
        if (!$config) return $row;

        $mapping = []; // map file header => config key

        // build label->key map
        $labelMap = [];
        foreach ($config['headers_to_db'] as $key => $meta) {
            $label = $meta['label'] ?? $key;
            $labelMap[strtolower($label)] = $key;
            $labelMap[strtolower($key)] = $key; // also key itself
        }

        $normalized = [];
        foreach ($row as $fileHeader => $value) {
            $lk = strtolower(trim($fileHeader));
            $targetKey = $labelMap[$lk] ?? null;
            if ($targetKey) {
                $normalized[$targetKey] = $value;
            } else {
                // try to sanitize header (replace spaces, lower)
                $candidate = str_replace(' ', '_', $lk);
                $candidate = preg_replace('/[^a-z0-9_]/','', $candidate);
                $normalized[$candidate] = $value;
            }
        }

        return $normalized;
    }
}
