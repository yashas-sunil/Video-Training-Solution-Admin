@extends('adminlte::page')

@section('title', 'Assign Students')

@section('content_header')
    <h1>Assign Students to Batch</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            Batch: <strong>{{ $batch->batch_name }}</strong>
        </h3>
    </div>

    <div class="card-body">

        <form action="{{ route('batches.storeStudents', $batch->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Select Students</label>

                <select name="students[]" 
                        class="form-control select2" 
                        multiple="multiple" 
                        required>

                    @foreach($students as $student)
                        <option value="{{ $student->id }}"
                            @if(in_array($student->id, $assignedStudents))
                                selected
                            @endif
                        >
                            {{ $student->name }} ({{ $student->email }})
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-success">
                    Save Assignment
                </button>

                <a href="{{ route('batches.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>

        </form>

    </div>
</div>

@stop


@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
/* Dropdown max height + scroll */
.select2-results__options {
    max-height: 300px !important;
    overflow-y: auto !important;
}

/* Selected box height */
.select2-selection--multiple {
    min-height: 45px !important;
    max-height: 120px !important;
    overflow-y: auto !important;
}

/* Clean look */
.select2-container--default .select2-selection--multiple {
    border-radius: 6px;
    border: 1px solid #ced4da;
    padding: 5px;
}

/* Hover effect */
.select2-results__option--highlighted {
    background-color: #700002 !important;
    color: #fff !important;
}
</style>

@stop


@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Search and select students",
        allowClear: true,
        width: '100%'
    });
});
</script>
@stop
