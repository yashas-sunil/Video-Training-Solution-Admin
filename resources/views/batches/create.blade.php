@extends('adminlte::page')

@section('title', 'Create Batch')

@section('content_header')
    <h1>Create New Batch</h1>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .card-custom {
            border-top: 3px solid #700002;
        }

        .is-invalid {
            border: 1px solid #dc3545 !important;
        }

        .is-valid {
            border: 1px solid #28a745 !important;
        }
    </style>
@stop


@section('content')

    <div class="card card-custom shadow-sm">
        <div class="card-body">

            <form action="{{ route('batches.store') }}" method="POST" id="batchForm">
                @csrf

                <div class="row">

                    {{-- Batch Name --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Batch Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Enter Batch Name">
                            <small class="text-danger" id="nameError"></small>
                        </div>
                    </div>

                    {{-- Multiple Courses --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Courses</label>
                            <select name="course_ids[]" id="course_ids" class="form-control select2" multiple="multiple">
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="courseError"></small>
                        </div>
                    </div>

                    {{-- Start Date --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control">
                            <small class="text-danger" id="startError"></small>
                        </div>
                    </div>

                    {{-- Expiry Date --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" class="form-control">
                            <small class="text-danger" id="expiryError"></small>
                        </div>
                    </div>

                </div>

                <div class="d-flex" style="gap:10px;">
                            <a href="{{ route('batches.index') }}" class="btn btn-secondary">
                                Back
                            </a>

                            <button id="submitBtn" type="submit" class="btn btn-success">
                                Save
                            </button>
                        </div>

            </form>

        </div>
    </div>

@stop



@section('js')

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            // Select2 Init
            $('.select2').select2({
                placeholder: "Select multiple courses",
                allowClear: true,
                width: '100%'
            });

        });


        /* =========================
           CALENDAR CLICK FULL OPEN
        ========================= */

        document.querySelectorAll('input[type="date"]').forEach(function(input) {
            input.addEventListener('click', function() {
                if (this.showPicker) {
                    this.showPicker();
                }
            });
        });


        /* =========================
           LIVE VALIDATION REMOVE
        ========================= */

        function setValid(id) {
            document.getElementById(id).classList.remove('is-invalid');
            document.getElementById(id).classList.add('is-valid');
        }

        function setInvalid(id) {
            document.getElementById(id).classList.remove('is-valid');
            document.getElementById(id).classList.add('is-invalid');
        }


        // Batch Name
        document.getElementById('name').addEventListener('input', function() {
            if (this.value.trim() !== "") {
                document.getElementById('nameError').innerText = "";
                setValid('name');
            }
        });

        // Courses
        $('#course_ids').on('change', function() {
            const courses = $(this).val();
            if (courses && courses.length > 0) {
                document.getElementById('courseError').innerText = "";
                document.getElementById('course_ids').classList.remove('is-invalid');
            }
        });

        // Start Date
        document.getElementById('start_date').addEventListener('change', function() {
            if (this.value !== "") {
                document.getElementById('startError').innerText = "";
                setValid('start_date');
            }
        });

        // Expiry Date
        document.getElementById('expiry_date').addEventListener('change', function() {
            if (this.value !== "") {
                document.getElementById('expiryError').innerText = "";
                setValid('expiry_date');
            }
        });


        document.getElementById('batchForm').addEventListener('submit', function(e) {

            let valid = true;

            const name = document.getElementById('name').value.trim();
            const courses = $('#course_ids').val();
            const start = document.getElementById('start_date').value;
            const expiry = document.getElementById('expiry_date').value;

            document.getElementById('nameError').innerText = "";
            document.getElementById('courseError').innerText = "";
            document.getElementById('startError').innerText = "";
            document.getElementById('expiryError').innerText = "";

            if (name === "") {
                document.getElementById('nameError').innerText = "Batch name is required";
                setInvalid('name');
                valid = false;
            }

            if (!courses || courses.length === 0) {
                document.getElementById('courseError').innerText = "Please select at least one course";
                document.getElementById('course_ids').classList.add('is-invalid');
                valid = false;
            }

            if (start === "") {
                document.getElementById('startError').innerText = "Start date is required";
                setInvalid('start_date');
                valid = false;
            }

            if (expiry === "") {
                document.getElementById('expiryError').innerText = "Expiry date is required";
                setInvalid('expiry_date');
                valid = false;
            }

            if (start && expiry && start > expiry) {
                document.getElementById('expiryError').innerText = "Expiry date must be after start date";
                setInvalid('expiry_date');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }

        });
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date();

            let year = today.getFullYear();
            let month = String(today.getMonth() + 1).padStart(2, '0');
            let day = String(today.getDate()).padStart(2, '0');

            let minDate = `${year}-${month}-${day}`;

            document.getElementById("start_date").setAttribute("min", minDate);
        });

        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date();

            let year = today.getFullYear();
            let month = String(today.getMonth() + 1).padStart(2, '0');
            let day = String(today.getDate()).padStart(2, '0');

            let minDate = `${year}-${month}-${day}`;

            document.getElementById("expiry_date").setAttribute("min", minDate);
        });
    </script>

@stop
