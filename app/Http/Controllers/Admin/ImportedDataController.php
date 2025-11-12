<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\Controller;

class ImportedDataController extends Controller
{
    public function index()
    {
        // List all import types and file keys
        $all = config('imports');
        return view('imported.index', ['imports' => $all]);
    }

    public function show($importType, $fileKey, Request $request)
    {
        $cfg = config("imports.{$importType}.files.{$fileKey}") ?? abort(404);
        $table = "{$importType}_{$fileKey}";
        $columns = array_keys($cfg['headers_to_db']);

        $q = DB::table($table);

        if ($search = $request->query('search')) {
            $q->where(function($sub) use ($columns, $search) {
                foreach ($columns as $col) {
                    $sub->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        $data = $q->paginate(20)->appends($request->query());

        return view('imported.show', compact('data','columns','importType','fileKey','cfg'));
    }

    public function export($importType, $fileKey, Request $request)
    {
        $cfg = config("imports.{$importType}.files.{$fileKey}") ?? abort(404);
        $table = "{$importType}_{$fileKey}";
        $columns = array_keys($cfg['headers_to_db']);

        $q = DB::table($table);

        if ($search = $request->query('search')) {
            $q->where(function($sub) use ($columns, $search) {
                foreach ($columns as $col) {
                    $sub->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        $rows = $q->get();

        // simple CSV streamed response to avoid dependency
        $filename = "{$importType}_{$fileKey}_export_".date('Ymd_His').".csv";
        $response = new StreamedResponse(function() use ($rows, $columns) {
            $out = fopen('php://output','w');
            // headers
            fputcsv($out, $columns);
            foreach ($rows as $r) {
                $line = [];
                foreach ($columns as $c) $line[] = $r->{$c} ?? null;
                fputcsv($out, $line);
            }
            fclose($out);
        });

        $response->headers->set('Content-Type','text/csv');
        $response->headers->set('Content-Disposition','attachment; filename="'.$filename.'"');
        return $response;
    }

    public function deleteRow($importType, $fileKey, $id, Request $request)
    {
        $cfg = config("imports.{$importType}");
        $perm = $cfg['permission_required'] ?? null;
        if ($perm && !$request->user()->can($perm)) {
            abort(403);
        }

        $table = "{$importType}_{$fileKey}";
        DB::table($table)->where('id', $id)->delete();

        return back()->with('status','Row deleted');
    }

    public function audits($importType, $fileKey, $rowId)
    {
        $table = "{$importType}_{$fileKey}";
        $audits = DB::table('import_audits')->where('table_name', $table)->where('row_id', $rowId)->get();
        return view('imported.audits', compact('audits'));
    }
}
