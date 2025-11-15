<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\ImportRepository;
use App\Services\Export\CsvExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\Controller;

class ImportedDataController extends Controller
{
    private ImportRepository $importRepository;
    private CsvExporter $csvExporter;
    public function __construct()
    {
        $this->importRepository = new ImportRepository();
        $this->csvExporter = new CsvExporter();
    }
    public function index()
    {
        $imports = config('imports');
        return view('imported.index', ['imports' => $imports]);
    }

    public function show($importType, $fileKey, Request $request)
    {
        [$table, $columns, $cfg] = $this->getTableAndHeadersColumnsAndConfig($importType, $fileKey);

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
        [$table, $columns] = $this->getTableAndHeadersColumnsAndConfig($importType, $fileKey);
        $rows = $this->importRepository->getRows($table, $columns, $request->query('search'));

        $filename = "{$importType}_{$fileKey}_export_".date('Ymd_His').".csv";

        return response()->stream(function() use ($rows, $columns) {
            $this->csvExporter->export($rows, $columns);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\""
        ]);
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

    private function getTableAndHeadersColumnsAndConfig($importType, $fileKey)
    {
        $cfg = config("imports.{$importType}.files.{$fileKey}") ?? abort(404);
        $table = "{$importType}_{$fileKey}";
        $columns = array_keys($cfg['headers_to_db']);

        return [$table, $columns, $cfg];
    }
}
