@extends('layouts.admin')


@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@endsection

@section('content')
    <p>Welcome, {{ auth()->user()->name }}!</p>

    <div class="row">
        {{-- Example cards --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\User::count() }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        {{-- Add more cards here --}}
    </div>
@endsection



