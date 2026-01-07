@extends('adminlte::page')

@section('title', 'Add Chapter & Upload SCORM')

@section('content_header')
    <h1 class="m-0 text-dark">Add Chapter & Upload SCORM</h1>
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

        .upload-toggle.active {
            color: #fff;
        }
    </style>

    <div id="loaderOverlay">
        <div class="spinner-border text-primary"></div>
    </div>

    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-md-10 col-lg-9">
                <div class="card custom-card">
                    <div class="card-body">

                        {{-- Messages --}}
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="d-flex mb-4" style="gap:10px;">
                            <button type="button" class="btn btn-primary upload-toggle active" data-target="scormForm">
                                SCORM Upload
                            </button>
                            <button type="button" class="btn btn-outline-primary upload-toggle" data-target="manualForm">
                                Manual Upload
                            </button>
                        </div>

                        <form method="POST" action="{{ route('chapter.scorm.upload') }}" enctype="multipart/form-data"
                            id="scormForm">
                            @csrf

                            {{-- Course Select --}}
                            <div class="mb-4">
                                <label class="form-label">Select Course <span class="text-danger">*</span></label>
                                <select name="course_id" class="form-control" required>
                                    <option value="">-- Select Course --</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Chapter Name --}}
                            <div class="mb-4">
                                <label class="form-label">Chapter Name <span class="text-danger">*</span></label>
                                <input type="text" name="chapter_name" class="form-control"
                                    placeholder="Enter chapter name" required>
                            </div>

                            {{-- SCORM ZIP --}}
                            <div class="mb-4">
                                <label class="form-label">SCORM Zip File <span class="text-danger">*</span></label>
                                <input type="file" name="zip_file" class="form-control" accept=".zip" required>
                            </div>

                            {{-- Watch Time --}}
                            {{-- <div class="mb-4">
                            <label class="form-label">Total Watch Time (minutes) <span class="text-danger">*</span></label>
                            <input type="number" name="watch_time" class="form-control"
                                   placeholder="e.g. 90" min="1" required>
                        </div> --}}

                            {{-- View Limit --}}
                            {{-- <div class="mb-4">
                            <label class="form-label">Validity (View Limit) <span class="text-danger">*</span></label>
                            <select name="view_limit_option" id="view_limit_option"
                                    class="form-control" onchange="toggleCustomViewLimit()" required>
                                <option value="">-- Select --</option>
                                <option value="1">1 View</option>
                                <option value="2">2 Views</option>
                                <option value="3">3 Views</option>
                                <option value="4">4 Views</option>
                                <option value="5">5 Views</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div> --}}

                            {{-- Custom Limit --}}
                            {{-- <div class="mb-4" id="customViewLimitDiv" style="display:none;">
                            <label class="form-label">Custom View Limit</label>
                            <input type="number" name="view_limit" id="view_limit"
                                   class="form-control" min="1">
                        </div> --}}

                            <div class="d-flex" style="gap:10px;">
                                <a href="{{ route('chapters') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back
                                </a>

                                <button id="scormSubmitBtn" type="submit" class="btn btn-success">
                                    Save <i class="fas fa-save ml-1"></i>
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('chapter.manual.upload') }}" enctype="multipart/form-data"
                            id="manualForm" style="display:none;">
                            @csrf

                            {{-- Course Select --}}
                            <div class="mb-4">
                                <label class="form-label">Select Course <span class="text-danger">*</span></label>
                                <select name="course_id" class="form-control" required>
                                    <option value="">-- Select Course --</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Chapter Name --}}
                            <div class="mb-4">
                                <label class="form-label">Chapter Name <span class="text-danger">*</span></label>
                                <input type="text" name="chapter_name" class="form-control"
                                    placeholder="Enter chapter name" required>
                            </div>

                            <p class="text-muted mb-3">Upload any file type for each slot. Only the files you pick will
                                be saved.</p>

                            @php
                                $manualTypes = [
                                    ['key' => 'detailed_trainer_slides', 'label' => 'Detailed Trainer Slides'],
                                    ['key' => 'glossary', 'label' => 'Glossary'],
                                    ['key' => 'infographics', 'label' => 'Infographics'],
                                    ['key' => 'summary_slides', 'label' => 'Summary Slides'],
                                    ['key' => 'textbook', 'label' => 'Textbook'],
                                    ['key' => 'videos', 'label' => 'Videos'],
                                    ['key' => 'map', 'label' => 'Map'],
                                ];
                            @endphp

                            <div class="row">
                                @foreach ($manualTypes as $type)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ $type['label'] }}</label>
                                        <input
                                            type="file"
                                            class="form-control"
                                            name="manual_{{ $type['key'] }}[]"
                                            accept="*/*"
                                            multiple
                                        >
                                        <small class="text-muted">
                                            Optional · you can select multiple files (pdf / images / video / docs)
                                        </small>
                                    </div>
                                @endforeach
                    </div>

                            <div class="d-flex" style="gap:10px;">
                                <a href="{{ route('chapters') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back
                                </a>

                                <button id="manualSubmitBtn" type="submit" class="btn btn-success">
                                    Save Manual Upload <i class="fas fa-save ml-1"></i>
                                </button>
                            </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        // function toggleCustomViewLimit() {
        //     let v = document.getElementById('view_limit_option').value;
        //     let div = document.getElementById('customViewLimitDiv');
        //     let input = document.getElementById('view_limit');

        //     if (v === 'custom') {
        //         div.style.display = 'block';
        //         input.required = true;
        //     } else {
        //         div.style.display = 'none';
        //         input.required = false;
        //         input.value = v;
        //     }
        // }

        // document.getElementById('uploadForm').addEventListener('submit', function() {
        //     document.getElementById('submitBtn').disabled = true;
        //     document.getElementById('loaderOverlay').style.display = 'flex';
        // });

        const toggles = document.querySelectorAll('.upload-toggle');
        const forms = {
            scormForm: document.getElementById('scormForm'),
            manualForm: document.getElementById('manualForm'),
        };

        toggles.forEach((btn) => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-target');

                Object.keys(forms).forEach((key) => {
                    forms[key].style.display = key === target ? 'block' : 'none';
                });

                toggles.forEach((toggle) => {
                    toggle.classList.remove('active', 'btn-primary');
                    toggle.classList.add('btn-outline-primary');
                });

                btn.classList.add('active', 'btn-primary');
                btn.classList.remove('btn-outline-primary');
            });
        });

        const loader = document.getElementById('loaderOverlay');

        document.getElementById('scormForm').addEventListener('submit', function () {
            document.getElementById('scormSubmitBtn').disabled = true;
            loader.style.display = 'flex';
        });

        document.getElementById('manualForm').addEventListener('submit', function () {
            document.getElementById('manualSubmitBtn').disabled = true;
            loader.style.display = 'flex';
        });
    </script>

@endsection
