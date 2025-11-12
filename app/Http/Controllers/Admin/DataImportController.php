<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\ProcessImport;
use App\Notifications\ImportStartedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

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
            'files.*' => 'required|file|mimes:csv,xlsx,txt|max:10240',
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
                abort(403);
            }

            $file = $request->file('files.' . $fileKey);
            $ext = strtolower($file->getClientOriginalExtension());

            if (!in_array($ext, ['csv','xlsx','xls','txt'])) {
                return back()->withErrors('Unsupported file type');
            }

            $path = $file->store('imports', 'public');

            $importId = DB::table('imports')->insertGetId([
                'user_id' => $request->user()->id,
                'import_type' => $importType,
                'file_key' => $fileKey,
                'original_file_name' => $file->getClientOriginalName(),
                'stored_file_path' => $path,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Notification::send($request->user(), new ImportStartedNotification($importId, $importType, $fileKey));

            ProcessImport::dispatch($importId);
        }



        return redirect()
            ->route('admin.data-import.index')
            ->with('success','Import is in progress. You will be notified when complete.');
    }
}
