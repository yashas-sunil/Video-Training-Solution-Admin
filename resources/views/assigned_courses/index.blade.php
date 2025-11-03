@extends('adminlte::page')

@section('title', 'Assigned Courses')

@section('content_header')
    <div class="row">
        <div class="col">
            <h1 class="m-0 text-dark">Assigned Courses</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('assigned-courses.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Assign New Course
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card mt-3">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Assigned Courses List</h3>
            </div>
            <div class="card-body">
                {!! $html->table(['id' => 'assignedCourseTable', 'class' => 'table table-bordered table-striped'], true) !!}
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
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
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
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    <script>
        $(document).on('click', '.status-toggle', function() {
            let id = $(this).data('id');
            let currentStatus = $(this).data('status');

            $.ajax({
                url: `/assigned-courses/toggle-status/${id}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#assignedCourseTable').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500
                    });
                }
            });
        });
    </script>
@stop
