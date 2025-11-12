@extends('adminlte::page')

@section('title', 'Create User')

@section('content_header')
    <h1>Create User</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">New User</h3>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Permissions</label>
                            <div>
                                @foreach(\App\Models\Permission::all() as $permission)
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                               class="form-check-input">
                                        <label class="form-check-label">{{ $permission->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Create User</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
