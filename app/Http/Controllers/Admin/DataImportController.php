<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ImportStatus;
use App\Jobs\ProcessImport;
use App\Models\Import;
use App\Notifications\ImportStartedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class DataImportController
{
    public function index()
    {
        $all = config('imports');
        $user = Auth::user();

        $importTypes = [];
        foreach ($all as $key => $cfg) {
            $perm = $cfg['permission_required'] ?? null;
            if (!$perm || $user->can($perm)) {
                $importTypes[$key] = $cfg;
            }
        }

        return view('admin.data-import.index', compact('importTypes'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_type' => 'required|string',
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:csv,xlsx|max:10240',
        ]);

        $importType = $request->input('import_type');

        $fileKeys = $request->file('files');

        foreach ($fileKeys as $fileKey => $fileValue) {
            $cfg = config("imports.{$importType}.files.{$fileKey}") ?? null;

            if (!$cfg) {
                return back()->withErrors("Invalid import type/file");
            }

            $perm = config("imports.{$importType}.permission_required");
            if ($perm && !$request->user()->can($perm)) {
                return back()->withErrors('You do not have permission to import this type.');
            }

            $file = $request->file('files.' . $fileKey);
            $ext = strtolower($file->getClientOriginalExtension());

            if (!in_array($ext, ['csv','xlsx','xls'])) {
                return back()->withErrors('Unsupported file type');
            }

            $requiredHeaders = array_map(
                fn($col) => strtolower($col['label'] ?? ''),
                $cfg['headers_to_db'] ?? []
            );
            $fileHeaders = $this->getHeadersFromFile($file);
            $missing = array_diff($requiredHeaders, $fileHeaders);

            if (!empty($missing)) {
                return back()->withErrors("Missing required headers in {$fileKey}: " . implode(', ', $missing));
            }

            $path = $file->store('imports', 'public');

            $import = Import::create([
                'user_id' => $request->user()->id,
                'import_type' => $importType,
                'file_key' => $fileKey,
                'original_file_name' => $file->getClientOriginalName(),
                'stored_file_path' => $path,
                'status' => ImportStatus::PENDING,
                'message' => ImportStatus::PENDING_MESSAGE,
            ]);

            Notification::send($request->user(), new ImportStartedNotification($import->id, $importType, $fileKey));

            ProcessImport::dispatch($import->id);
        }

        return redirect()
            ->route('admin.data-import.index')
            ->with('success','Import is in progress. You will be notified when complete.');
    }

    private function getHeadersFromFile($file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());

        try {
            if (in_array($ext, ['xls', 'xlsx'])) {
                $reader = match($ext) {
                    'xls' => new Xls(),
                    'xlsx' => new Xlsx(),
                };

                $reader->setReadDataOnly(true);

                $spreadsheet = $reader->load($file->getRealPath());

                if ($spreadsheet->getSheetCount() === 0) return [];

                $sheet = $spreadsheet->getSheet(0);

                $firstRow = $sheet->rangeToArray(
                    'A1:' . $sheet->getHighestColumn() . '1',
                    null,
                    true,
                    true,
                    true
                )[1] ?? [];

                return array_map(fn($h) => strtolower(trim($h)), array_values($firstRow));
            }

            if (in_array($ext, ['csv', 'txt'])) {
                $handle = fopen($file->getRealPath(), 'r');
                $headers = fgetcsv($handle, 0, ',') ?: [];
                fclose($handle);

                return array_map(fn($h) => strtolower(trim($h)), $headers);
            }

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [];
        }

        return [];
    }
}
