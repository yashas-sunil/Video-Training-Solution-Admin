@extends('adminlte::page')

@section('title', 'Assign Course')

@section('content_header')
    <h1 class="text-left text-dark mb-3">Assign Course to User</h1>
@stop

@section('content')
    <div class="d-flex justify-content-left">
        <div class="card shadow-sm col-md-8 " style="border-radius: 10px;">
            <div class="card-body">
                {{-- Success --}}
                @if (session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: '{{ session('success') }}',
                                toast: true,
                                position: 'top-end',
                                timer: 4000,
                                showConfirmButton: false
                            });
                        });
                    </script>
                @endif

                {{-- Error --}}
                @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: '{{ session('error') }}',
                                toast: true,
                                position: 'top-end',
                                timer: 4000,
                                showConfirmButton: false
                            });
                        });
                    </script>
                @endif

                {{-- Validation --}}
                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('assigned-courses.store') }}" method="POST">
                    @csrf

                    {{-- User --}}
                    <div class="form-group mb-3">
                        <label for="user_id" class="font-weight-bold">
                            Select User <span class="text-danger">*</span>
                        </label>
                        <select name="user_id" id="user_id" class="form-control form-control-sm select2" required>
                            <option value="">-- Select User --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Course --}}
                    <div class="form-group mb-3">
                        <label for="course_id" class="font-weight-bold">
                            Select Course <span class="text-danger">*</span>
                        </label>
                        <select name="course_id" id="course_id" class="form-control form-control-sm select2" required>
                            <option value="">-- Select Course --</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Expire Date & Time --}}
                    <div class="form-group mb-3">
                        <label for="expire_date" class="font-weight-bold">Expire Date & Time</label>
                        <input type="datetime-local" name="expire_date" id="expire_date"
                            class="form-control form-control-sm" placeholder="Select date and time"
                            onfocus="this.showPicker()" onkeydown="return false" onpaste="return false">
                    </div>

                    <div class="d-flex mt-4" style="gap: 10px">
                        <a href="{{ route('assigned-courses.index') }}" class="btn btn-secondary px-3">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                        <button type="submit" class="btn btn-success px-3">
                            Assign Course <i class="fas fa-save mr-1"></i> 
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JS Libraries --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: "Select an option",
                allowClear: true
            });

            // Fetch expire date dynamically when user and course selected
            function fetchExpireDate() {
                let userId = $('#user_id').val();
                let courseId = $('#course_id').val();

                if (userId && courseId) {
                    fetch(`/course-expire-date/${courseId}?user_id=${userId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.expire_date) {
                                $('#expire_date').val(data.expire_date.replace(' ', 'T'));
                            }
                        })
                        .catch(err => console.error(err));
                }
            }

            $('#user_id, #course_id').on('change', fetchExpireDate);
        });
    </script>
@stop
