@extends('layouts.admin')

@section('title', 'Row Audits')

@section('content_header')
    <h1>Row Audit Log</h1>
@endsection

@section('content')

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Changes for this row</h3>
        </div>

        <div class="box-body">

            @if($audits->isEmpty())
                <p class="text-muted">No audits for this row.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Column</th>
                        <th>Old</th>
                        <th>New</th>
                        <th>When</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($audits as $a)
                        <tr>
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
