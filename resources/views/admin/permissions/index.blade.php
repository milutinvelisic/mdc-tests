@extends('layouts.admin')

@section('title', 'Permissions')

@section('content_header')
    <h1>Permissions</h1>
@endsection

@section('content')
    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary mb-3">Add Permission</a>

{{--    @if(session('success'))--}}
{{--        <x-adminlte-alert theme="success" title="Success" dismissable>--}}
{{--            {{ session('success') }}--}}
{{--        </x-adminlte-alert>--}}
{{--    @endif--}}

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($permissions as $permission)
                    <tr>
                        <td>{{ $permission->id }}</td>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->description }}</td>
                        <td>
                            <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?');" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>
@endsection
