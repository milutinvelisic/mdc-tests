@extends('layouts.admin')

@section('title', 'Data Import')

@section('content_header')
    <h1>Data Import</h1>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="data-import-form" action="{{ route('admin.data-import.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="import_type">Select Import Type</label>
            <select name="import_type" id="import_type" class="form-control" required>
                <option value="">-- Select --</option>
                @foreach($importTypes as $key => $import)
                    <option value="{{ $key }}">{{ $import['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div id="files-container"></div>

        <button type="submit" class="btn btn-primary mt-2">Start Import</button>
    </form>

    <script>
        const importConfig = @json($importTypes);

        document.getElementById('import_type').addEventListener('change', function() {
            const type = this.value;
            const container = document.getElementById('files-container');
            container.innerHTML = '';

            if (!type) return;

            const files = importConfig[type].files;
            Object.keys(files).forEach(fileKey => {
                const file = files[fileKey];
                const div = document.createElement('div');
                div.classList.add('form-group');
                div.innerHTML = `
                <label>${file.label}</label>
                <input type="file" name="files[${fileKey}]" class="form-control-file">
                <small class="form-text text-muted">Required headers: ${Object.keys(file.headers_to_db).join(', ')}</small>
            `;
                container.appendChild(div);
            });
        });
    </script>
@stop
