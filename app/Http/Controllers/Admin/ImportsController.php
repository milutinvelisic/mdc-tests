<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ImportsController extends Controller
{
    public function index()
    {
        $imports = DB::table('imports')->orderBy('created_at','desc')->paginate(20);
        return view('imports.list', compact('imports'));
    }

    public function show($id)
    {
        $import = DB::table('imports')->where('id', $id)->first();
        if (!$import) abort(404);

        $errors = DB::table('import_errors')->where('import_id', $id)->get();
        $audits = DB::table('import_audits')->where('import_id', $id)->get();

        return view('imports.show', compact('import','errors','audits'));
    }
}
