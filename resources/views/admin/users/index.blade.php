@extends('layouts.admin')

@section('title', 'Users')

@section('content_header')
    <h1>Users</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Create User</a>
@endsection

@section('content')
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Name</th><th>Email</th><th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-info">Edit</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete user?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
