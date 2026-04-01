@extends('adminlte::page')

@section('title', 'Subjects List')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Subjects List</h3>

            <div class="card-tools">
                <a href="{{ route('subjects.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Subjects
                </a>
            </div>
        </div>

        <div class="card-body">

            {!! $html->table(['class' => 'table table-bordered table-striped table-hover'], true) !!}

        </div>

    </div>

</div>

@endsection


@section('js')

{!! $html->scripts() !!}

<!-- ✅ Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- ✅ Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    toastr.options = {
        closeButton: true,
        progressBar: true,
        timeOut: 5000, // 5 sec
        positionClass: "toast-top-right"
    };

    @if (session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if (session('error'))
        toastr.error("{{ session('error') }}");
    @endif

    @if (session('warning'))
        toastr.warning("{{ session('warning') }}");
    @endif

    @if (session('info'))
        toastr.info("{{ session('info') }}");
    @endif

});
</script>

@endsection