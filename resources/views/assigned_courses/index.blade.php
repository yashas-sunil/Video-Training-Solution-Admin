@extends('adminlte::page')

@section('title', 'Assigned Courses')
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
        margin-bottom: 0px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 26px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: orange;
        /* green active color */
    }

    input:checked+.slider:before {
        transform: translateX(24px);
    }
    .badge-info  {
        font-size: 16px!important;
        background-color: #17a3b83d!important;
        color: #17a2b8!important;
    }
    .badge-secondary {
        font-size: 16px!important;
        background-color: #4d4d4d49!important;
        color: #4d4d4d!important;
    }
</style>
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
        <div class="card-header bg-white text-dark">
            <h3 class="card-title">Assigned Courses List</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            {!! $html->table(['id' => 'assignedCourseTable', 'class' => 'table table-bordered table-striped'], true) !!}
            </div>
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
    $(document).on('change', '.toggle-status', function () {
    let id = $(this).data('id');
    let status = $(this).is(':checked') ? 1 : 0; // Get the current checkbox state

    $.ajax({
        url: `/assigned-courses/toggle-status/${id}`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            status: status
        },
        success: function (response) {
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
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: response.message || 'Could not update status.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        },
        error: function () {
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