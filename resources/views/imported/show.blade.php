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
            {{-- Filter + Export --}}
            <form class="form-inline mb-2" method="GET">
                <div class="form-group">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Search all columns...">
                </div>
                <button class="btn btn-primary btn-sm">Filter</button>

                <a href="{{ route('admin.imported.export', [$importType,$fileKey]) . '?search=' . request('search') }}"
                   class="btn btn-success btn-sm ml-2">
                    <i class="fa fa-file-excel-o"></i> Export
                </a>
            </form>

            {{-- Data Table --}}
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    @foreach($columns as $c)
                        <th>{{ $cfg['headers_to_db'][$c]['label'] ?? $c }}</th>
                    @endforeach
                    <th style="width:150px;">Actions</th>
                </tr>
                </thead>

                <tbody>
                @foreach($data as $row)
                    <tr>
                        @foreach($columns as $c)
                            <td>{{ $row->{$c} }}</td>
                        @endforeach

                        <td>
                            {{-- Audits Modal Trigger --}}
                            @if(!empty($auditFields) && isset($rowAudits[$row->id]))
                                <button type="button" class="btn btn-info btn-xs" data-toggle="modal"
                                        data-target="#auditsModal-{{ $row->id }}">
                                    <i class="fa fa-list"></i> Audits
                                </button>
                            @endif

                            {{-- Delete Row --}}
                            <form action="{{ route('admin.imported.deleteRow', [$importType,$fileKey,$row->id]) }}"
                                  method="POST" style="display:inline-block;"
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

            {{-- Modals (placed outside table) --}}
            @foreach($data as $row)
                @if(!empty($auditFields) && isset($rowAudits[$row->id]))
                    <div class="modal fade" id="auditsModal-{{ $row->id }}" tabindex="-1" role="dialog"
                         aria-labelledby="auditsModalLabel-{{ $row->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="auditsModalLabel-{{ $row->id }}">
                                        Audits for Row #{{ $row->id }}
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @php
                                        $auditsForRow = $rowAudits[$row->id] ?? collect();
                                    @endphp

                                    @if($auditsForRow->isEmpty())
                                        <p>No audits found for this row.</p>
                                    @else
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Field</th>
                                                <th>Old Value</th>
                                                <th>New Value</th>
                                                <th>Changed At</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($auditFields as $field)
                                                @foreach($auditsForRow->where('column_key', $field) as $audit)
                                                    <tr>
                                                        <td>{{ $audit->column_key }}</td>
                                                        <td>{{ $audit->old_value }}</td>
                                                        <td>{{ $audit->new_value }}</td>
                                                        <td>{{ $audit->created_at }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
