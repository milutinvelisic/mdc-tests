@extends('layouts.admin')

@section('title', 'Imported Data')

@section('content_header')
    <h1>{{ $cfg['label'] }} ({{ $importType }} / {{ $fileKey }})</h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('admin.imported.index') }}">Imported Data</a></li>
        <li class="active">{{ $cfg['label'] }}</li>
    </ol>
@endsection

@section('content')

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">View Imported Records</h3>
        </div>

        <div class="box-body">

            <form class="form-inline" method="GET">
                <div class="form-group">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Search all columns...">
                </div>
                <button class="btn btn-primary btn-sm">Filter</button>

                <a href="{{ route('admin.imported.export', [$importType,$fileKey]) . '?search=' . request('search') }}"
                   class="btn btn-success btn-sm" style="margin-left: 10px;">
                    <i class="fa fa-file-excel-o"></i> Export
                </a>
            </form>

            <table class="table table-bordered table-striped" style="margin-top: 15px;">
                <thead>
                <tr>
                    @foreach($columns as $c)
                        <th>{{ $cfg['headers_to_db'][$c]['label'] }}</th>
                    @endforeach
                    <th style="width:130px;">Actions</th>
                </tr>
                </thead>

                <tbody>
                @foreach($data as $row)
                    <tr>
                        @foreach($columns as $c)
                            <td>{{ $row->{$c} }}</td>
                        @endforeach

                        <td>
                            <a class="btn btn-info btn-xs"
                               href="{{ route('admin.imported.audits', [$importType,$fileKey,$row->id]) }}">
                                <i class="fa fa-list"></i> Audits
                            </a>

                            <form action="{{ route('admin.imported.deleteRow', [$importType,$fileKey,$row->id]) }}"
                                  method="POST"
                                  style="display:inline-block;"
                                  onsubmit="return confirm('Delete this row?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-xs">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="text-center">
                {{ $data->links() }}
            </div>

        </div>
    </div>

@endsection
