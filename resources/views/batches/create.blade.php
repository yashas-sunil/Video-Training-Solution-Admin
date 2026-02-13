@extends('adminlte::page')

@section('title', 'Create Batch')

@section('content_header')
    <h1>Create New Batch</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('batches.store') }}" method="POST" id="batchForm">
            @csrf

            {{-- Batch Name --}}
            <div class="form-group">
                <label>Batch Name</label>
                <input type="text" 
                       name="name" 
                       id="name"
                       class="form-control"
                       value="{{ old('name') }}"
                       required>
                <small class="text-danger" id="nameError"></small>
            </div>

            {{-- Course Selection --}}
            <div class="form-group">
                <label>Select Course</label>
                <select name="course_id" 
                        id="course_id"
                        class="form-control"
                        required>
                    <option value="">-- Select Course --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
                <small class="text-danger" id="courseError"></small>
            </div>

            {{-- Start Date --}}
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" 
                       name="start_date" 
                       id="start_date"
                       class="form-control"
                       required>
                <small class="text-danger" id="startError"></small>
            </div>

            {{-- Expiry Date --}}
            <div class="form-group">
                <label>Expiry Date</label>
                <input type="date" 
                       name="expiry_date" 
                       id="expiry_date"
                       class="form-control"
                       required>
                <small class="text-danger" id="expiryError"></small>
            </div>

            {{-- Submit --}}
            <div class="form-group">
                <button type="submit" class="btn btn-success">
                    Create Batch
                </button>

                <a href="{{ route('batches.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

@stop


@section('js')
<script>

// 📅 Click anywhere on date field → open calendar
document.querySelectorAll('input[type="date"]').forEach(function(input) {
    input.addEventListener('click', function() {
        this.showPicker();   // modern browsers support
    });
});


// ✅ Frontend Validation
document.getElementById('batchForm').addEventListener('submit', function(e){

    let valid = true;

    const name = document.getElementById('name').value.trim();
    const course = document.getElementById('course_id').value;
    const start = document.getElementById('start_date').value;
    const expiry = document.getElementById('expiry_date').value;

    document.getElementById('nameError').innerText = "";
    document.getElementById('courseError').innerText = "";
    document.getElementById('startError').innerText = "";
    document.getElementById('expiryError').innerText = "";

    if(name === ""){
        document.getElementById('nameError').innerText = "Batch name is required";
        valid = false;
    }

    if(course === ""){
        document.getElementById('courseError').innerText = "Please select a course";
        valid = false;
    }

    if(start === ""){
        document.getElementById('startError').innerText = "Start date is required";
        valid = false;
    }

    if(expiry === ""){
        document.getElementById('expiryError').innerText = "Expiry date is required";
        valid = false;
    }

    if(start && expiry && start > expiry){
        document.getElementById('expiryError').innerText = "Expiry date must be after start date";
        valid = false;
    }

    if(!valid){
        e.preventDefault();
    }

});

</script>
@stop
