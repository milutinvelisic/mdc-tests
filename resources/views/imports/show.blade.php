@extends('layouts.admin')

@section('title', 'Import Details')

@section('content_header')
    <h1>Import #{{ $import->id }} Details</h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('admin.imports.index') }}">Imports</a></li>
        <li class="active">Import #{{ $import->id }}</li>
    </ol>
@endsection

@section('content')

    <div class="box box-primary">

        <div class="box-header with-border">
            <h3 class="box-title">Summary</h3>
        </div>

        <div class="box-body">
            <p><strong>Import Type:</strong> {{ $import->import_type }} / {{ $import->file_key }}</p>
            <p><strong>Status:</strong>
                @if($import->status == 'completed')
                    <span class="label label-success">Completed</span>
                @elseif($import->status == 'failed')
                    <span class="label label-danger">Failed</span>
                @elseif($import->status == 'running')
                    <span class="label label-warning">Running</span>
                @else
                    <span class="label label-default">Pending</span>
                @endif
            </p>
            <p><strong>Message:</strong> {{ $import->message }}</p>
        </div>

    </div>


    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title">Validation Errors</h3>
        </div>

        <div class="box-body">
            @if($errors->isEmpty())
                <p class="text-muted">No validation errors.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Row</th>
                        <th>Column</th>
                        <th>Value</th>
                        <th>Message</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($errors as $error)
                        <tr>
                            <td>{{ $error->row_number }}</td>
                            <td>{{ $error->column_key }}</td>
                            <td>{{ $error->value }}</td>
                            <td>{{ $error->message }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>


    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Audit Logs (Updated Rows)</h3>
        </div>

        <div class="box-body">
            @if($audits->isEmpty())
                <p class="text-muted">No audit entries.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Row ID</th>
                        <th>Column</th>
                        <th>Old Value</th>
                        <th>New Value</th>
                        <th>When</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($audits as $a)
                        <tr>
                            <td>{{ $a->row_id }}</td>
                            <td>{{ $a->column_key }}</td>
                            <td>{{ $a->old_value }}</td>
                            <td>{{ $a->new_value }}</td>
                            <td>{{ $a->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

@endsection
