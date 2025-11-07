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
        .button-footer {
            display: flex;
            gap: 10px;
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

                            <div class="mb-4">
                                <label for="title" class="form-label">Course Title <span class="text-danger"> *</span></label>
                                <input type="text" name="title" id="title" class="form-control"
                                    placeholder="Enter course title..." required>
                            </div>

                            <div class="mb-4">
                                <label for="zip_file" class="form-label">SCORM Zip File <span class="text-danger"> *</span></label>
                                <input type="file" name="zip_file" id="zip_file" style="height: 43px" class="form-control" accept=".zip"
                                    required>
                            </div>

                            <div class="mb-4">
                                <label for="watch_time" class="form-label">Total Watch Time (in minutes) <span class="text-danger"> *</span></label>
                                <input type="number" name="watch_time" id="watch_time" class="form-control"
                                    placeholder="e.g. 90" min="1" required>
                            </div>

                            <div class="mb-4">
                                <label for="view_limit_option" class="form-label">Validity (View Limit) <span class="text-danger"> *</span></label>
                                <select name="view_limit_option" id="view_limit_option" class="form-control" required
                                    onchange="toggleCustomViewLimit()">
                                    <option value="">-- Select Limit --</option>
                                    <option value="1">1 View</option>
                                    <option value="2">2 Views</option>
                                    <option value="3">3 Views</option>
                                    <option value="4">4 Views</option>
                                    <option value="5">5 Views</option>
                                    <option value="custom">Enter Custom Value</option>
                                </select>
                            </div>

                            <div class="mb-4" id="customViewLimitDiv" style="display: none;">
                                <label for="view_limit" class="form-label">Enter Custom View Limit</label>
                                <input type="number" name="view_limit" id="view_limit" class="form-control"
                                    placeholder="e.g. 10" min="1">
                            </div>
                        </form>
                         <div class="d-flex" style="gap: 10px">
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
                            <button type="submit" class="btn btn-success">Upload Course<i class="fas fa-save ml-1"></i></button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function toggleCustomViewLimit() {
            var selectValue = document.getElementById('view_limit_option').value;
            var customDiv = document.getElementById('customViewLimitDiv');
            var customInput = document.getElementById('view_limit');

            if (selectValue === 'custom') {
                customDiv.style.display = 'block';
                customInput.required = true;
            } else {
                customDiv.style.display = 'none';
                customInput.required = false;
                if (selectValue !== '') {
                    customInput.value = selectValue;
                } else {
                    customInput.value = '';
                }
            }
        }

        document.getElementById('uploadForm').addEventListener('submit', function() {
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('loaderOverlay').style.display = 'flex';
        });

        document.addEventListener("DOMContentLoaded", function() {
            toggleCustomViewLimit();
        });
    </script>
@endsection
