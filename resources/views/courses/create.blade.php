@extends('adminlte::page')

@section('title', 'Upload SCORM Course')

@section('content_header')
    <h1 class="m-0 text-dark">Upload New SCORM Course</h1>
@stop

@section('content')

    <style>
        #loaderOverlay {
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.7);
            width: 100%;
            height: 100%;
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .custom-card {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 600;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, .25);
        }
    </style>

    <div id="loaderOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Uploading...</span>
        </div>
    </div>

    <div class="container-fluid px-4">
        <div class="row justify-content-left">
            <div class="col-md-8 col-lg-8">
                <div class="card custom-card">
                    <div class="card-body">

                        {{-- Flash Messages --}}
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form method="POST" action="{{ route('scorm.upload') }}" enctype="multipart/form-data"
                            id="uploadForm">
                            @csrf

                            {{-- Course Title --}}
                            <div class="mb-4">
                                <label class="form-label">Course Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                    placeholder="Enter course title...">

                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- SCORM ZIP File --}}
                            <div class="mb-4">
                                <label class="form-label">SCORM Zip File <span class="text-danger">*</span></label>
                                <input type="file" name="zip_file" id="zip_file"
                                    class="form-control @error('zip_file') is-invalid @enderror" accept=".zip">

                                @error('zip_file')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Watch Time --}}
                            <div class="mb-4">
                                <label class="form-label">Total Watch Time (minutes) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="watch_time" id="watch_time"
                                    class="form-control @error('watch_time') is-invalid @enderror"
                                    value="{{ old('watch_time') }}" placeholder="e.g. 90" min="1">

                                @error('watch_time')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- View Limit Select --}}
                            <div class="mb-4">
                                <label class="form-label">Validity (View Limit) <span class="text-danger">*</span></label>
                                <select name="view_limit_option" id="view_limit_option"
                                    class="form-control @error('view_limit') is-invalid @enderror"
                                    onchange="toggleCustomViewLimit()">
                                    <option value="">-- Select Limit --</option>
                                    <option value="1" {{ old('view_limit_option') == 1 ? 'selected' : '' }}>1 View
                                    </option>
                                    <option value="2" {{ old('view_limit_option') == 2 ? 'selected' : '' }}>2 Views
                                    </option>
                                    <option value="3" {{ old('view_limit_option') == 3 ? 'selected' : '' }}>3 Views
                                    </option>
                                    <option value="4" {{ old('view_limit_option') == 4 ? 'selected' : '' }}>4 Views
                                    </option>
                                    <option value="5" {{ old('view_limit_option') == 5 ? 'selected' : '' }}>5 Views
                                    </option>
                                    <option value="custom" {{ old('view_limit_option') == 'custom' ? 'selected' : '' }}>
                                        Enter Custom Value</option>
                                </select>

                                @error('view_limit_option')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Custom View Limit --}}
                            <div class="mb-4" id="customViewLimitDiv" style="display:none;">
                                <label class="form-label">Enter Custom View Limit</label>
                                <input type="number" name="view_limit" id="view_limit"
                                    class="form-control @error('view_limit') is-invalid @enderror"
                                    value="{{ old('view_limit') }}" placeholder="e.g. 10" min="1">

                                @error('view_limit')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="d-flex" style="gap: 10px;">
                                <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back
                                </a>

                                <button id="submitBtn" type="submit" class="btn btn-success">
                                    Upload Course <i class="fas fa-save ml-1"></i>
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- JS --}}
    <script>
        // Toggle Custom Limit
        function toggleCustomViewLimit() {
            let select = document.getElementById('view_limit_option').value;
            let div = document.getElementById('customViewLimitDiv');
            let input = document.getElementById('view_limit');

            if (select === 'custom') {
                div.style.display = 'block';
                input.required = true;
            } else {
                div.style.display = 'none';
                input.required = false;
                input.value = select !== '' ? select : '';
            }
        }

        // FORM VALIDATION BEFORE SUBMIT
        document.getElementById('uploadForm').addEventListener('submit', function(event) {

            let title = document.getElementById("title").value.trim();
            let watch = document.getElementById("watch_time").value.trim();

            document.querySelectorAll(".text-danger-custom").forEach(e => e.remove());
            let error = false;

            if (title.length === 0) {
                showError("title", "Course title cannot be empty.");
                error = true;
            }
            if (watch.length === 0 || parseInt(watch) < 1) {
                showError("watch_time", "Watch time must be at least 1 minute.");
                error = true;
            }

            if (error) {
                event.preventDefault();
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('loaderOverlay').style.display = 'none';
                return false;
            }

            document.getElementById('submitBtn').disabled = true;
            document.getElementById('loaderOverlay').style.display = 'flex';
        });

        function showError(field, msg) {
            let el = document.getElementById(field);
            el.insertAdjacentHTML("afterend",
                `<div class="text-danger text-danger-custom">${msg}</div>`);
        }

        document.addEventListener("DOMContentLoaded", function() {
            toggleCustomViewLimit();
        });
    </script>

@endsection
