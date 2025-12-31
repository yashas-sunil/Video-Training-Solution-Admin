@extends('adminlte::page')

@section('title', 'Chapter List')

{{-- Toastr CSS --}}
@section('css')
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .dt-buttons {
        display: none !important;
    }
</style>
@endsection


@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Chapter List</h1>

    <a href="{{ route('chapter.scorm.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Add Chapter
    </a>
</div>
@stop


@section('content')
<div class="card">
    <div class="card-body">
        {!! $html->table(['class' => 'table table-bordered table-striped'], true) !!}
    </div>
</div>
@stop


@section('js')
{{-- Datatable Scripts --}}
{!! $html->scripts() !!}

{{-- Toastr JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    toastr.options = {
        closeButton: true,
        progressBar: true,
        timeOut: 3000,
        positionClass: "toast-top-right"
    };

    @if (session('success'))
        toastr.success("{{ session('success') }}", "Success");
    @endif

    @if (session('error'))
        toastr.error("{{ session('error') }}", "Error");
    @endif

    @if (session('warning'))
        toastr.warning("{{ session('warning') }}", "Warning");
    @endif

    @if (session('info'))
        toastr.info("{{ session('info') }}", "Info");
    @endif
</script>
@endsection
