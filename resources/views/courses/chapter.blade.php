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

        /* Validation styles */
        .field-error {
            color: #dc3545;
            font-size: 0.82rem;
            margin-top: 4px;
            display: none;
        }

        .is-invalid-field {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15) !important;
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

                        {{-- ─── SCORM FORM ─── --}}
                        <form method="POST" action="{{ route('chapter.scorm.upload') }}" enctype="multipart/form-data"
                            id="scormForm">
                            @csrf

                            {{-- Course --}}
                            <div class="mb-4">
                                <label class="form-label">Select Course <span class="text-danger">*</span></label>
                                <select name="course_id" id="scormCourseId" class="form-control"
                                    onchange="clearError('scormCourseId', 'scormCourseError')">
                                    <option value="">-- Select Course --</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                <small class="field-error" id="scormCourseError">Please select a Course.</small>
                            </div>

                            {{-- Subject --}}
                            <div class="mb-4">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <select name="subject_id" id="scormChapterId" class="form-control" disabled
                                    onchange="clearError('scormChapterId', 'scormSubjectError')">
                                    <option value="">-- Select Subject --</option>
                                </select>
                                <small class="field-error" id="scormSubjectError">Please select a Subject.</small>
                            </div>

                            {{-- Chapter Name --}}
                            <div class="mb-4">
                                <label class="form-label">Chapter Name <span class="text-danger">*</span></label>
                                <input type="text" name="chapter_name" id="scormChapterName" class="form-control"
                                    placeholder="Enter chapter name"
                                    oninput="clearError('scormChapterName', 'scormChapterNameError')">
                                <small class="field-error" id="scormChapterNameError">Please enter a Chapter Name.</small>
                                @if ($errors->has('chapter_name'))
                                    <div class="alert alert-danger mt-1" id="scormChapterError">{{ $errors->first('chapter_name') }}</div>
                                    <script>
                                        setTimeout(function() {
                                            const el = document.getElementById('scormChapterError');
                                            if (el) { el.style.transition='opacity 0.5s'; el.style.opacity='0'; setTimeout(()=>el.remove(),500); }
                                        }, 5000);
                                    </script>
                                @endif
                            </div>

                            {{-- SCORM ZIP --}}
                            <div class="mb-4">
                                <label class="form-label">SCORM Zip File <span class="text-danger">*</span></label>
                                <input type="file" name="zip_file" id="scormZipFile" class="form-control" accept=".zip"
                                    onchange="clearError('scormZipFile', 'scormZipError')">
                                <small class="field-error" id="scormZipError">Please upload a SCORM Zip file.</small>
                            </div>

                            <div class="d-flex" style="gap:10px;">
                                <a href="{{ route('chapters') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back
                                </a>
                                <button id="scormSubmitBtn" type="button" class="btn btn-success"
                                    onclick="validateAndSubmit('scorm')">
                                    Save <i class="fas fa-save ml-1"></i>
                                </button>
                            </div>
                        </form>

                        {{-- ─── MANUAL FORM ─── --}}
                        <form method="POST" action="{{ route('chapter.manual.upload') }}" enctype="multipart/form-data"
                            id="manualForm" style="display:none;">
                            @csrf

                            {{-- Course --}}
                            <div class="mb-4">
                                <label class="form-label">Select Course <span class="text-danger">*</span></label>
                                <select name="course_id" id="manualCourseId" class="form-control"
                                    onchange="clearError('manualCourseId', 'manualCourseError')">
                                    <option value="">-- Select Course --</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                <small class="field-error" id="manualCourseError">Please select a Course.</small>
                            </div>

                            {{-- Subject --}}
                            <div class="mb-4">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <select name="subject_id" id="manualChapterId" class="form-control" disabled
                                    onchange="clearError('manualChapterId', 'manualSubjectError')">
                                    <option value="">-- Select Subject --</option>
                                </select>
                                <small class="field-error" id="manualSubjectError">Please select a Subject.</small>
                            </div>

                            {{-- Chapter Name --}}
                            <div class="mb-4">
                                <label class="form-label">Chapter Name <span class="text-danger">*</span></label>
                                <input type="text" name="chapter_name" id="manualChapterName" class="form-control"
                                    placeholder="Enter chapter name"
                                    oninput="clearError('manualChapterName', 'manualChapterNameError')">
                                <small class="field-error" id="manualChapterNameError">Please enter a Chapter Name.</small>
                                @if ($errors->has('chapter_name'))
                                    <div class="alert alert-danger mt-1" id="manualChapterError">{{ $errors->first('chapter_name') }}</div>
                                    <script>
                                        setTimeout(function() {
                                            const el = document.getElementById('manualChapterError');
                                            if (el) { el.style.transition='opacity 0.5s'; el.style.opacity='0'; setTimeout(()=>el.remove(),250); }
                                        }, 2500);
                                    </script>
                                @endif
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
                                            ['key' => 'glossary',      'label' => 'Glossary',      'icon' => 'fa-list'],
                                            ['key' => 'infographics',  'label' => 'Infographics',  'icon' => 'fa-image'],
                                            ['key' => 'textbook',      'label' => 'Textbook',      'icon' => 'fa-book-open'],
                                            ['key' => 'map',           'label' => 'Map',           'icon' => 'fa-map'],
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
                                <small class="field-error d-block mb-2" id="lessonsError">Please add at least one lesson.</small>
                                <div id="lessonsContainer"></div>
                            </div>

                            <div class="d-flex mt-4" style="gap:10px;">
                                <a href="{{ route('chapters') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back
                                </a>
                                <button id="manualSubmitBtn" type="button" class="btn btn-success"
                                    onclick="validateAndSubmit('manual')">
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

        let lessonCount  = 0;
        let activeLessons = [];

        /* ─── VALIDATION ─── */
        function validateAndSubmit(formType) {
            let isValid = true;

            if (formType === 'scorm') {

                // Course
                if (!document.getElementById('scormCourseId').value) {
                    showError('scormCourseId', 'scormCourseError');
                    isValid = false;
                }
                // Subject
                if (!document.getElementById('scormChapterId').value) {
                    showError('scormChapterId', 'scormSubjectError');
                    isValid = false;
                }
                // Chapter Name
                if (!document.getElementById('scormChapterName').value.trim()) {
                    showError('scormChapterName', 'scormChapterNameError');
                    isValid = false;
                }
                // ZIP file
                if (!document.getElementById('scormZipFile').files.length) {
                    showError('scormZipFile', 'scormZipError');
                    isValid = false;
                }

                if (isValid) {
                    document.getElementById('scormSubmitBtn').disabled = true;
                    document.getElementById('loaderOverlay').style.display = 'flex';
                    document.getElementById('scormForm').submit();
                }

            } else {

                // Course
                if (!document.getElementById('manualCourseId').value) {
                    showError('manualCourseId', 'manualCourseError');
                    isValid = false;
                }
                // Subject
                if (!document.getElementById('manualChapterId').value) {
                    showError('manualChapterId', 'manualSubjectError');
                    isValid = false;
                }
                // Chapter Name
                if (!document.getElementById('manualChapterName').value.trim()) {
                    showError('manualChapterName', 'manualChapterNameError');
                    isValid = false;
                }
                // Lessons
                const container = document.getElementById('lessonsContainer');
                if (container.children.length === 0) {
                    let err = document.getElementById('lessonsError');
                    err.style.display = 'block';
                    isValid = false;
                }

                if (isValid) {
                    document.getElementById('manualSubmitBtn').disabled = true;
                    document.getElementById('loaderOverlay').style.display = 'flex';
                    document.getElementById('manualForm').submit();
                }
            }
        }

        /* Show error */
        function showError(fieldId, errorId) {
            let field = document.getElementById(fieldId);
            let error = document.getElementById(errorId);
            if (field)  field.classList.add('is-invalid-field');
            if (error)  error.style.display = 'block';
        }

        /* Clear error on input/change */
        function clearError(fieldId, errorId) {
            let field = document.getElementById(fieldId);
            let error = document.getElementById(errorId);
            if (field)  field.classList.remove('is-invalid-field');
            if (error)  error.style.display = 'none';
        }

        /* ─── LESSONS ─── */
        function addLesson() {
            lessonCount++;
            activeLessons.push(lessonCount);

            // Hide lessons error when at least one is added
            document.getElementById('lessonsError').style.display = 'none';

            const container  = document.getElementById('lessonsContainer');
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
                               placeholder="e.g., Introduction to Topic">
                        <small class="field-error lesson-name-error">Please enter a Lesson Name.</small>
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

            // Attach live clear on lesson name input
            const newCard = document.getElementById(`lesson-${lessonCount}`);
            const nameInput = newCard.querySelector('.lesson-name-input');
            const nameError = newCard.querySelector('.lesson-name-error');
            nameInput.addEventListener('input', function () {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid-field');
                    nameError.style.display = 'none';
                }
            });
        }

        function removeLesson(lessonId) {
            const lessonCard = document.getElementById(`lesson-${lessonId}`);
            if (lessonCard) {
                if (confirm('Are you sure you want to remove this lesson?')) {
                    lessonCard.remove();
                    const index = activeLessons.indexOf(lessonId);
                    if (index > -1) activeLessons.splice(index, 1);
                    reindexLessons();
                }
            }
        }

        function reindexLessons() {
            const lessonCards = document.querySelectorAll('#lessonsContainer .lesson-card');
            lessonCards.forEach((card, index) => {
                const header = card.querySelector('.lesson-header h6');
                if (header) header.innerHTML = `<i class="fas fa-chalkboard-teacher"></i> Lesson ${index + 1}`;

                card.querySelectorAll('input[name^="lessons"]').forEach(input => {
                    input.setAttribute('name', input.getAttribute('name').replace(/lessons\[\d+\]/, `lessons[${index}]`));
                });
            });
        }

        /* ─── FORM TOGGLE ─── */
        const toggles = document.querySelectorAll('.upload-toggle');
        const forms = {
            scormForm:  document.getElementById('scormForm'),
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

        /* ─── DOM READY ─── */
        document.addEventListener('DOMContentLoaded', function () {

            // Auto-add first lesson when manual tab is clicked
            const manualToggle = document.querySelector('[data-target="manualForm"]');
            if (manualToggle) {
                manualToggle.addEventListener('click', function () {
                    const container = document.getElementById('lessonsContainer');
                    if (container && container.children.length === 0) addLesson();
                });
            }

            // SCORM cascading dropdown
            document.getElementById('scormCourseId').addEventListener('change', function () {
                const courseId = this.value;
                if (courseId) {
                    fetchSubjects(courseId, document.getElementById('scormChapterId'));
                } else {
                    resetSubject(document.getElementById('scormChapterId'));
                }
            });

            // Manual cascading dropdown
            document.getElementById('manualCourseId').addEventListener('change', function () {
                const courseId = this.value;
                if (courseId) {
                    fetchSubjects(courseId, document.getElementById('manualChapterId'));
                } else {
                    resetSubject(document.getElementById('manualChapterId'));
                }
            });
        });

        function resetSubject(selectEl) {
            selectEl.innerHTML = '<option value="">-- Select Subject --</option>';
            selectEl.disabled  = true;
        }

        function fetchSubjects(courseId, selectElement) {
            fetch(`/api/subjects/${courseId}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network error');
                    return res.json();
                })
                .then(data => {
                    let html = '<option value="">-- Select Subject --</option>';
                    if (data.subjects && data.subjects.length > 0) {
                        data.subjects.forEach(s => {
                            html += `<option value="${s.id}">${s.name}</option>`;
                        });
                    }
                    selectElement.innerHTML  = html;
                    selectElement.disabled   = false;
                })
                .catch(err => {
                    console.error('Error fetching subjects:', err);
                    selectElement.innerHTML = '<option value="">-- Error Loading Subjects --</option>';
                    selectElement.disabled  = true;
                });
        }

    </script>

@endsection