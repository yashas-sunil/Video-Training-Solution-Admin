@extends('layouts.app')

@section('content')
<style>
    /* Spinner Overlay */
    #loaderOverlay {
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(255,255,255,0.8);
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
</style>

<div id="loaderOverlay">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Uploading...</span>
    </div>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">📦 Upload SCORM Course</h4>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('scorm.upload') }}" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Course Title <span class="text-danger"> *</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Enter course title" required>
                        </div>

                        <div class="mb-3">
                            <label for="zip_file" class="form-label">SCORM Zip File <span class="text-danger"> *</span></label>
                            <input type="file" name="zip_file" id="zip_file" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100" id="submitBtn">⬆️ Upload Course</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Show loader on form submit
    document.getElementById('uploadForm').addEventListener('submit', function () {
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('loaderOverlay').style.display = 'flex';
    });
</script>
@endsection
