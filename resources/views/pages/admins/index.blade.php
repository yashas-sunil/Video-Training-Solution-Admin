@extends('adminlte::page')

@section('title', 'Admin')

@section('content_header')
    <div class="row">
        <div class="col">
            <h1 class="m-0 text-dark">Users</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('admins.create') }}" type="button" class="btn btn-success"><i class="fas fa-plus"></i> Create</a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                {!! $html->table(['id' => 'datatable'], true) !!}
            </div>
        </div>
    </div>
@stop

@section('js')
    {!! $html->scripts() !!}

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    position: 'top-end',
                    toast: true
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    position: 'top-end',
                    toast: true
                });
            });
        </script>
    @endif
@stop
