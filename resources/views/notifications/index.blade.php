@extends('layouts.admin')

@section('title', 'Notifications')

@section('content_header')
    <h1>Notifications</h1>
@endsection

@section('content')

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">All Notifications</h3>
        </div>

        <div class="box-body">

            @foreach($notifications as $n)
                <div class="alert alert-{{ $n->data['level'] == 'error' ? 'danger' : ($n->data['level'] == 'success' ? 'success' : 'info') }}">
                    <strong>{{ $n->data['title'] }}:</strong> {{ $n->data['message'] }}

                    @if($n->read_at == null)
                        <a href="{{ route('notifications.read', $n->id) }}"
                           class="btn btn-xs btn-primary pull-right">Mark as read</a>
                    @endif
                </div>
            @endforeach

        </div>

        <div class="box-footer text-center">
            {{ $notifications->links() }}
        </div>
    </div>

@endsection
