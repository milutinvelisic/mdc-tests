@extends('layouts.admin')

@section('title', 'Imports History')

@section('content_header')
    <h1>Imports History</h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Imports</li>
    </ol>
@endsection

@section('content')

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">All Imports</h3>
        </div>

        <div class="box-body">

            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>File Key</th>
                    <th>File Name</th>
                    <th>Status</th>
                    <th>Uploaded At</th>
                    <th style="width:90px;">View</th>
                </tr>
                </thead>

                <tbody>
                @foreach($imports as $imp)
                    <tr>
                        <td>{{ $imp->id }}</td>
                        <td>{{ $imp->user_id }}</td>
                        <td>{{ $imp->import_type }}</td>
                        <td>{{ $imp->file_key }}</td>
                        <td>{{ $imp->original_file_name }}</td>
                        <td>
                            @if($imp->status == 'completed')
                                <span class="label label-success">Completed</span>
                            @elseif($imp->status == 'failed')
                                <span class="label label-danger">Failed</span>
                            @elseif($imp->status == 'running')
                                <span class="label label-warning">Running</span>
                            @else
                                <span class="label label-default">Pending</span>
                            @endif
                        </td>
                        <td>{{ $imp->created_at }}</td>
                        <td>
                            <a href="{{ route('admin.imports.show', $imp->id) }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-eye"></i> Open
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>

            <div class="text-center">
                {{ $imports->links() }}
            </div>

        </div>
    </div>

@endsection
