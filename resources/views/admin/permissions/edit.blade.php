@extends('layouts.admin')

@section('title', 'Edit Permission')

@section('content_header')
    <h1>Edit Permission</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Permission Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $permission->name) }}" required>
                    @error('name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mt-2">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control">{{ old('description', $permission->description) }}</textarea>
                    @error('description')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success mt-3">Update</button>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary mt-3">Cancel</a>
            </form>
        </div>
    </div>
@endsection
