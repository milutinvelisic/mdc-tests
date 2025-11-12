@extends('layouts.admin')

@section('title', 'Create Permission')

@section('content_header')
    <h1>Create Permission</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Permission Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mt-2">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                    @error('description')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success mt-3">Save</button>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary mt-3">Cancel</a>
            </form>
        </div>
    </div>
@endsection
