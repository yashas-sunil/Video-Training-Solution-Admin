@extends('adminlte::page')

@section('title', 'Assign Course')

@section('content_header')
    <h1>Assign Course to User</h1>
@stop

@section('content')
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .form-group {
            max-width: 400px;
            margin-bottom: 15px;
        }

        .card-body {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        label {
            font-weight: 600;
        }
    </style>

    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Assign Course</h3>
            </div>

            {{--  Success --}}
            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: '{{ session('success') }}',
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                            position: 'top-end',
                            toast: true
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
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                            position: 'top-end',
                            toast: true
                        });
                    });
                </script>
            @endif

            {{-- Validation --}}
            @if ($errors->any())
                <div class="alert alert-danger m-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Assign Course Form --}}
            <form action="{{ route('assigned-courses.store') }}" method="POST">
                @csrf
                <div class="card-body">

                    {{-- Select User --}}
                    <div class="form-group">
                        <label for="user_id">Select User <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-control select2" required>
                            <option value="">-- Select User --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Select Course --}}
                    <div class="form-group">
                        <label for="course_id">Select Course <span class="text-danger">*</span></label>
                        <select name="course_id" id="course_id" class="form-control select2" required>
                            <option value="">-- Select Course --</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Expire Date --}}
                    <div class="form-group">
                        <label for="expire_date">Expire Date</label>
                        <input type="datetime-local" name="expire_date" id="expire_date" class="form-control" readonly
                            onfocus="this.showPicker()" placeholder="Select date only from picker">
                    </div>

                </div>

                <div class="card-footer text-right d-flex justify-content-between">
                    <div class="ml-auto">
                        <a href="{{ route('assigned-courses.index') }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Assign Course
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Select2 & AJAX --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: "Select an option",
                allowClear: true
            });

            $('#expire_date').on('keydown paste', function(e) {
                e.preventDefault();
            });

            function fetchExpireDate() {
                let userId = $('#user_id').val();
                let courseId = $('#course_id').val();

                if (userId && courseId) {
                    fetch(`/course-expire-date/${courseId}?user_id=${userId}`)
                        .then(response => response.json())
                        .then(data => {
                            $('#expire_date').val(data.expire_date ? data.expire_date.replace(' ', 'T') : '');
                        })
                        .catch(err => console.error(err));
                }
            }

            $('#user_id, #course_id').on('change', fetchExpireDate);
        });
    </script>
@stop
