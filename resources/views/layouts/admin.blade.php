@extends('adminlte::page')

@section('js')
    @parent
    @if(session('success'))
        <script>
            $(function() {
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: 'Success',
                    body: @json(session('success')),
                    autohide: true,
                    delay: 5000
                });
            });
        </script>
    @endif
@endsection
