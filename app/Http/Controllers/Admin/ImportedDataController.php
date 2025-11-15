<?php

namespace App\Http\Controllers\Admin;

use App\Interfaces\ExporterInterface;
use App\Repositories\ImportRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ImportedDataController extends Controller
{
    private ImportRepository $importRepository;
    private ExporterInterface $exporter;

    public function __construct(ImportRepository $importRepository, ExporterInterface $exporter)
    {
        $this->importRepository = $importRepository;
        $this->exporter = $exporter;
    }
    public function index()
    {
        $imports = config('imports');
        return view('imported.index', ['imports' => $imports]);
    }

    public function show($importType, $fileKey, Request $request)
    {
        [$table, $columns, $cfg, $permission] = $this->getTableAndHeadersColumnsAndConfigAndPermission($importType, $fileKey);

        $data = $this->importRepository->getPaginatedRows($table, $columns, $request->query('search'), 20);
        $auditFields = $cfg['update_or_create'] ?? [];

        $rowAudits = $this->importRepository->getRowAudits($table, $data->pluck('id')->toArray(), $auditFields);

        return view('imported.show', compact(
            'data',
            'columns',
                'importType',
                'fileKey',
                'cfg',
                'rowAudits',
                'auditFields',
                'permission',
            )
        );
    }

    public function export($importType, $fileKey, Request $request)
    {
        [$table, $columns] = $this->getTableAndHeadersColumnsAndConfigAndPermission($importType, $fileKey);
        $rows = $this->importRepository->getRows($table, $columns, $request->query('search'));

        $extension = $this->exporter->getExtension();
        $filename = "{$importType}_{$fileKey}_export_" . date('Ymd_His') . ".{$extension}";

        $data = $rows->map(function($row) use ($columns) {
            return array_map(fn($c) => $row->{$c} ?? null, $columns);
        })->toArray();

        return $this->exporter->toStream($columns, $data, $filename);
    }

    public function deleteRow($importType, $fileKey, $id, Request $request)
    {
        $this->importRepository->deleteRow($importType, $fileKey, $id, $request->user());

        return back()->with('status','Row deleted');
    }

    public function audits($importType, $fileKey, $rowId)
    {
        $audits = $this->importRepository->getAudits($importType, $fileKey, $rowId);
        return view('imported.audits', compact('audits'));
    }

    private function getTableAndHeadersColumnsAndConfigAndPermission($importType, $fileKey)
    {
        $cfg = config("imports.{$importType}.files.{$fileKey}") ?? abort(404);
        $table = "{$importType}_{$fileKey}";
        $columns = array_keys($cfg['headers_to_db']);
        $permission = config("imports.{$importType}")['permission_required'];

        return [$table, $columns, $cfg, $permission];
    }
}
