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

        .lesson-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #f8f9fa;
        }

        .lesson-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }

        .remove-lesson-btn {
            padding: 5px 10px;
            font-size: 12px;
        }

        .content-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>

    <div id="loaderOverlay">
        <div class="spinner-border text-primary"></div>
    </div>

    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-md-12 col-lg-11">
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

                        {{-- SCORM FORM --}}
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

                        {{-- MANUAL FORM --}}
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

                            {{-- Chapter-Level Content --}}
                            <div class="content-section">
                                <h5 class="mb-3">
                                    <i class="fas fa-book"></i> Chapter-Level Content 
                                    <small class="text-muted">(Upload once for entire chapter)</small>
                                </h5>
                                
                                <div class="row">
                                    @php
                                        $chapterLevelTypes = [
                                            ['key' => 'glossary', 'label' => 'Glossary', 'icon' => 'fa-list'],
                                            ['key' => 'infographics', 'label' => 'Infographics', 'icon' => 'fa-image'],
                                            ['key' => 'textbook', 'label' => 'Textbook', 'icon' => 'fa-book-open'],
                                            ['key' => 'map', 'label' => 'Map', 'icon' => 'fa-map'],
                                        ];
                                    @endphp

                                    @foreach ($chapterLevelTypes as $type)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                <i class="fas {{ $type['icon'] }}"></i> {{ $type['label'] }}
                                            </label>
                                            <input type="file" class="form-control" 
                                                   name="manual_{{ $type['key'] }}[]" 
                                                   accept="*/*" multiple>
                                            <small class="text-muted">Optional · Multiple files allowed</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Lessons Section --}}
                            <div class="content-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-graduation-cap"></i> Lessons 
                                        <small class="text-muted">(Add content per lesson)</small>
                                    </h5>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addLesson()">
                                        <i class="fas fa-plus"></i> Add Lesson
                                    </button>
                                </div>

                                <div id="lessonsContainer">
                                    <!-- Lesson templates will be added here dynamically -->
                                </div>
                            </div>

                            <div class="d-flex mt-4" style="gap:10px;">
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
        
        // Variables
        let lessonCount = 0;
        let activeLessons = [];

        // Function to add lesson
        function addLesson() {
            lessonCount++;
            activeLessons.push(lessonCount);
            
            const container = document.getElementById('lessonsContainer');
            const currentIndex = activeLessons.length - 1;
            
            const lessonHTML = `
                <div class="lesson-card" id="lesson-${lessonCount}" data-lesson-id="${lessonCount}">
                    <div class="lesson-header">
                        <h6 class="mb-0">
                            <i class="fas fa-chalkboard-teacher"></i> Lesson ${activeLessons.length}
                        </h6>
                        <button type="button" class="btn btn-sm btn-danger remove-lesson-btn" 
                                onclick="removeLesson(${lessonCount})">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lesson Name <span class="text-danger">*</span></label>
                        <input type="text" name="lessons[${currentIndex}][lesson_name]" 
                               class="form-control lesson-name-input" 
                               placeholder="e.g., Introduction to Topic" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-file-powerpoint"></i> Detailed Trainer Slides
                            </label>
                            <input type="file" class="form-control" 
                                   name="lessons[${currentIndex}][manual_detailed_trainer_slides][]" 
                                   accept="*/*" multiple>
                            <small class="text-muted">Optional · Multiple files</small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-file-pdf"></i> Summary Slides
                            </label>
                            <input type="file" class="form-control" 
                                   name="lessons[${currentIndex}][manual_summary_slides][]" 
                                   accept="*/*" multiple>
                            <small class="text-muted">Optional · Multiple files</small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                <i class="fas fa-video"></i> Videos
                            </label>
                            <input type="file" class="form-control" 
                                   name="lessons[${currentIndex}][manual_videos][]" 
                                   accept="video/*" multiple>
                            <small class="text-muted">Optional · Multiple files</small>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', lessonHTML);
        }

        // Function to remove lesson
        function removeLesson(lessonId) {
            const lessonCard = document.getElementById(`lesson-${lessonId}`);
            if (lessonCard) {
                if (confirm('Are you sure you want to remove this lesson?')) {
                    lessonCard.remove();
                    
                    const index = activeLessons.indexOf(lessonId);
                    if (index > -1) {
                        activeLessons.splice(index, 1);
                    }
                    
                    reindexLessons();
                }
            }
        }

        // Function to reindex lessons
        function reindexLessons() {
            const container = document.getElementById('lessonsContainer');
            const lessonCards = container.querySelectorAll('.lesson-card');
            
            lessonCards.forEach((card, index) => {
                const header = card.querySelector('.lesson-header h6');
                if (header) {
                    header.innerHTML = `<i class="fas fa-chalkboard-teacher"></i> Lesson ${index + 1}`;
                }
                
                const inputs = card.querySelectorAll('input[name^="lessons"]');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    const newName = name.replace(/lessons\[\d+\]/, `lessons[${index}]`);
                    input.setAttribute('name', newName);
                });
            });
        }

        // Toggle between forms
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

        // Loader overlay
        const loader = document.getElementById('loaderOverlay');

        document.getElementById('scormForm').addEventListener('submit', function () {
            document.getElementById('scormSubmitBtn').disabled = true;
            loader.style.display = 'flex';
        });

        document.getElementById('manualForm').addEventListener('submit', function (e) {
            const lessonsContainer = document.getElementById('lessonsContainer');
            if (lessonsContainer.children.length === 0) {
                e.preventDefault();
                alert('Please add at least one lesson before submitting.');
                return false;
            }
            document.getElementById('manualSubmitBtn').disabled = true;
            loader.style.display = 'flex';
        });

        // Add first lesson when manual form is shown
        document.addEventListener('DOMContentLoaded', function() {
            const manualToggle = document.querySelector('[data-target="manualForm"]');
            if (manualToggle) {
                manualToggle.addEventListener('click', function() {
                    const container = document.getElementById('lessonsContainer');
                    if (container && container.children.length === 0) {
                        addLesson();
                    }
                });
            }
        });
    </script>

@endsection