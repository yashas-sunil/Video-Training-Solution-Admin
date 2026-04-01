@extends('adminlte::page')

@section('title', 'Add Subjects')

@section('content')

    <div class="container-fluid">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">Add Subjects</h3>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success m-3" id="successAlert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->has('duplicate'))
                        <div class="alert alert-danger m-3" id="errorAlert">
                            {{ $errors->first('duplicate') }}
                        </div>
                    @endif

                    <form action="{{ route('subjects.storesubject') }}" method="POST">
                        @csrf

                        <div class="card-body">

                            <!-- Course Dropdown -->
                            <div class="form-group">
                                <label>Select Course</label>

                                <select name="course_id" id="course_id"
                                    class="form-control @error('course_id') is-invalid @enderror">

                                    <option value="">Select Course</option>

                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}"
                                            {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('course_id')
                                    <small class="text-danger" id="courseError">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Subjects -->
                            <div class="form-group">

                                <label>Subjects</label>

                                <div id="subject-wrapper">

                                    <div class="input-group mb-2">

                                        <input type="text" name="subjects[]" id="subjectInput"
                                            class="form-control @error('subjects.0') is-invalid @enderror"
                                            placeholder="Enter Subject Name" value="{{ old('subjects.0') }}">

                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-success" onclick="addMore()">+</button>
                                        </div>

                                    </div>

                                    @error('subjects.0')
                                        <small class="text-danger" id="subjectError">{{ $message }}</small>
                                    @enderror

                                </div>

                            </div>

                        </div>

                   <div class="d-flex mt-4" style="gap: 10px">
                        <a href="{{ route('subjects.index') }}" class="btn btn-secondary px-3">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                        <button type="submit" class="btn btn-success px-3">
                            Save <i class="fas fa-save mr-1"></i>
                        </button>
                    </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection


@section('js')

    <script>
        function addMore() {

            let html = `
    <div class="input-group mb-2">

        <input type="text"
               name="subjects[]"
               class="form-control"
               placeholder="Enter Subject Name">

        <div class="input-group-append">
            <button type="button"
                    class="btn btn-danger"
                    onclick="this.closest('.input-group').remove()">
                    -
            </button>
        </div>

    </div>
    `;

            document.getElementById('subject-wrapper')
                .insertAdjacentHTML('beforeend', html);
        }

        document.addEventListener("DOMContentLoaded", function() {

            let course = document.getElementById("course_id");
            let subject = document.getElementById("subjectInput");
            let successBox = document.getElementById("successAlert");
            let errorBox = document.getElementById("errorAlert");

            if (course) {
                course.addEventListener("change", function() {
                    this.classList.remove("is-invalid");
                    let err = document.getElementById("courseError");
                    if (err) err.remove();
                });
            }

            if (subject) {
                subject.addEventListener("input", function() {
                    this.classList.remove("is-invalid");
                    let err = document.getElementById("subjectError");
                    if (err) err.remove();
                });
            }

            if (successBox) {
                setTimeout(() => {
                    successBox.style.opacity = "0";
                    setTimeout(() => successBox.remove(), 500);
                }, 2000);
            }

            if (errorBox) {
                setTimeout(() => {
                    errorBox.style.opacity = "0";
                    setTimeout(() => errorBox.remove(), 500);
                }, 3000);
            }

        });
    </script>

@endsection
