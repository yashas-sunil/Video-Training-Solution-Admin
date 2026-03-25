@extends('adminlte::page')

@section('title', 'Edit Subject')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Edit Subject</h3>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger m-3" id="errorAlert">
                @foreach ($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- ✅ Success Message --}}
        @if(session('success'))
            <div class="alert alert-success m-3" id="successAlert">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('subjects.update',$subject->id) }}" method="POST">
            @csrf

            <div class="card-body">

                <!-- Course Dropdown -->
                <div class="form-group">
                    <label>Course</label>

                    <select name="course_id" class="form-control" required>
                        <option value="">Select Course</option>

                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"
                                {{ $subject->course_id == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Subject Name -->
                <div class="form-group">
                    <label>Subject Name</label>

                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ $subject->name }}"
                           required>
                </div>

            </div>

           <div class="d-flex mt-4" style="gap: 10px">
                        <a href="{{ route('subjects.index') }}" class="btn btn-secondary px-3">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                        <button type="submit" class="btn btn-success px-3">
                            Update <i class="fas fa-save mr-1"></i>
                        </button>
                    </div>

        </form>

    </div>

</div>

@endsection


@section('js')

<script>
document.addEventListener("DOMContentLoaded", function () {

    let errorBox = document.getElementById("errorAlert");
    let successBox = document.getElementById("successAlert");

    if (errorBox) {
        setTimeout(() => {
            errorBox.style.transition = "opacity 0.5s";
            errorBox.style.opacity = "0";

            setTimeout(() => errorBox.remove(), 500);
        }, 3000); 
    }

    if (successBox) {
        setTimeout(() => {
            successBox.style.transition = "opacity 0.5s";
            successBox.style.opacity = "0";

            setTimeout(() => successBox.remove(), 500);
        }, 3000); 
    }

});
</script>

@endsection