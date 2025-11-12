@extends('layouts.admin')

@section('title', 'Data Import')

@section('content_header')
    <h1>Data Import</h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Data Import</li>
    </ol>
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Upload Import File</h3>
        </div>

        <form action="{{ route('admin.data-import.import') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="box-body">
                <div class="form-group">
                    <label>Import Type</label>
                    <select name="import_type" id="import_type" class="form-control" required>
                        <option value="">Select...</option>
                        @foreach($types as $key => $cfg)
                            <option value="{{ $key }}">{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" id="fileKeyBox" style="display:none;">
                    <label>File</label>
                    <select name="file_key" id="file_key" class="form-control" required></select>
                </div>

                <div class="callout callout-info" id="requiredHeaders" style="display:none;"></div>

                <div class="form-group">
                    <label>Upload File (.csv, .xlsx)</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
            </div>

            <div class="box-footer">
                <button class="btn btn-primary pull-right">Start Import</button>
            </div>

        </form>
    </div>

@endsection

@section('js')
    <script>
        const config = @json($types);
        const importTypeEl = document.getElementById('import_type');
        const fileKeyEl = document.getElementById('file_key');
        const fileKeyBox = document.getElementById('fileKeyBox');
        const requiredHeaders = document.getElementById('requiredHeaders');

        importTypeEl.addEventListener('change', function () {
            const type = this.value;
            fileKeyEl.innerHTML = '';
            requiredHeaders.style.display = 'none';

            if (!type) {
                fileKeyBox.style.display = 'none';
                return;
            }

            const files = config[type].files;
            Object.entries(files).forEach(([key, val]) => {
                fileKeyEl.innerHTML += `<option value="${key}">${val.label}</option>`;
            });

            fileKeyBox.style.display = 'block';
            showHeaders(type, Object.keys(files)[0]);
        });

        fileKeyEl.addEventListener('change', function () {
            showHeaders(importTypeEl.value, this.value);
        });

        function showHeaders(type, file) {
            if (!type || !file) return;

            const headers = config[type].files[file].headers_to_db;

            let html = `<h4>Required Headers</h4><ul>`;
            Object.entries(headers).forEach(([key, data]) =>
                html += `<li><strong>${data.label}</strong> (key: ${key}) — validations: ${data.validation.join(', ')}</li>`
            );
            html += `</ul>`;

            requiredHeaders.innerHTML = html;
            requiredHeaders.style.display = 'block';
        }
    </script>
@endsection
