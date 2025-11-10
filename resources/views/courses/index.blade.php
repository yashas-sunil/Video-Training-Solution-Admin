@extends('adminlte::page')

@section('title', 'Courses')
<style>
.dt-buttons {
    display: none !important;
}
</style>

@section('content_header')
    <div class="row">
        <div class="col">
            <h1 class="m-0 text-dark">Courses</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('courses.create') }}" type="button" class="btn btn-success">
                <i class="fas fa-plus"></i> Add Course
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="table-responsive">
                    {!! $html->table(['id' => 'datatable'], true) !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    {!! $html->scripts() !!}

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}", "Success", {
                closeButton: true,
                progressBar: true,
                timeOut: 3000
            });
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}", "Error", {
                closeButton: true,
                progressBar: true,
                timeOut: 3000
            });
        @endif

        @if (session('warning'))
            toastr.warning("{{ session('warning') }}", "Warning", {
                closeButton: true,
                progressBar: true,
                timeOut: 3000
            });
        @endif

        @if (session('info'))
            toastr.info("{{ session('info') }}", "Info", {
                closeButton: true,
                progressBar: true,
                timeOut: 3000
            });
        @endif
    </script>
@stop
