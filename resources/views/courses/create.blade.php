@extends('adminlte::page')

@section('title', 'Upload Content')

@section('content_header')
    <h1 class="m-0 text-dark">Add Course</h1>
@stop

@section('content')

<style>
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

<div class="container-fluid px-4">
    <div class="row justify-content-left">
        <div class="col-md-8 col-lg-8">
            <div class="card custom-card">
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('scorm.upload') }}" id="uploadForm">
                        @csrf

                        {{-- Title --}}
                        <div class="mb-4">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title"
                                class="form-control" value="{{ old('title') }}"
                                placeholder="Enter title...">
                        </div>

                        <div class="mb-4">
    <label class="form-label">
        Total Watch Time (minutes) <span class="text-danger">*</span>
    </label>
    <input type="number" name="watch_time" id="watch_time"
        class="form-control"
        value="{{ old('watch_time') }}"
        placeholder="e.g. 90"
        min="1"
        max="1000000000"
        oninput="limitInput(this)">
    
    <span class="text-danger" id="watchError"></span>
</div>

                        {{-- View Limit --}}
                        <div class="mb-4">
                            <label class="form-label">Validity (View Limit) <span class="text-danger">*</span></label>
                            <select name="view_limit_option" id="view_limit_option" class="form-control"
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

                        {{-- Custom View Limit --}}
                        <div class="mb-4" id="customViewLimitDiv" style="display:none;">
                            <label class="form-label">Enter Custom View Limit</label>
                            <input type="number" name="view_limit" id="view_limit" class="form-control"
                                placeholder="e.g. 10" min="1">
                        </div>

                        <div class="d-flex" style="gap:10px;">
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                                Back
                            </a>

                            <button id="submitBtn" type="submit" class="btn btn-success">
                                Save
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
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

    document.getElementById('uploadForm').addEventListener('submit', function(event) {

        let title = document.getElementById("title").value.trim();
        let watch = document.getElementById("watch_time").value.trim();
        let viewOption = document.getElementById("view_limit_option").value;
        let customView = document.getElementById("view_limit").value.trim();

        let error = false;

        document.querySelectorAll(".text-danger-custom").forEach(e => e.remove());

        if (title.length === 0) {
            showError("title", "Title cannot be empty.");
            error = true;
        }

        if (watch.length === 0 || parseInt(watch) < 1) {
            showError("watch_time", "Watch time must be at least 1 minute.");
            error = true;
        }
        if (viewOption === "") {
            showError("view_limit_option", "Please select view limit.");
            error = true;
        }

        if (viewOption === "custom" && (customView === "" || parseInt(customView) < 1)) {
            showError("view_limit", "Please enter valid custom view limit.");
            error = true;
        }

        if (error) {
            event.preventDefault();
            return false;
        }

    });

    function showError(field, msg) {
        let el = document.getElementById(field);
        el.insertAdjacentHTML("afterend",
            `<div class="text-danger text-danger-custom">${msg}</div>`);
    }

    document.addEventListener("DOMContentLoaded", function() {
        toggleCustomViewLimit();
    });
    function limitInput(el) {
    let max = 3000;

    if (el.value.length > 10) {
        el.value = el.value.slice(0, 10); // max 10 digits
    }

    if (parseInt(el.value) > max) {
        document.getElementById("watchError").innerText = "Max allowed is 3000 minutes (50 hours)";
        el.value = max;
    } else {
        document.getElementById("watchError").innerText = "";
    }
}
</script>

@endsection