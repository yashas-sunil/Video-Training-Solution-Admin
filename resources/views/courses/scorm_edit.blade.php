@extends('adminlte::page')

@section('title', 'Edit SCORM Chapter')

@section('content_header')
    <h1 class="m-0 text-dark">Edit SCORM Chapter</h1>
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

        /* Validation styles */
        .field-error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 4px;
            display: none;
        }

        .is-invalid-field {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15) !important;
        }

        .current-scorm-info {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .current-scorm-info h6 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .scorm-preview-btn {
            margin-top: 10px;
        }
    </style>

    <div id="loaderOverlay">
        <div class="spinner-border text-primary"></div>
    </div>

    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-md-12 col-lg-10">
                <div class="card custom-card">
                    <div class="card-body">

                        {{-- Messages --}}
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{-- SCORM EDIT FORM --}}
                        <form method="POST" action="{{ route('chapter.scorm.update', $chapter->id) }}" 
                              enctype="multipart/form-data" id="scormEditForm">
                            @php
                                $selectedCourseId = old('course_id', $chapter->course_id ?? '');
                                $selectedSubjectId = old('subject_id', $chapter->subject_id ?? '');
                                $selectedCourseTitle = optional($courses->firstWhere('id', $selectedCourseId))->title ?? '-- Select Course --';
                                $selectedSubjectName = optional($subjects->firstWhere('id', $selectedSubjectId))->name ?? optional($chapter->subject)->name ?? '-- Select Subject --';
                            @endphp
                            @csrf
                            @method('PUT')


                            {{-- Course --}}
                            <div class="mb-4">
                                <label class="form-label">Select Course <span class="text-danger">*</span></label>
                                <select name="course_id" id="scormCourseId" class="form-control"
                                    onchange="clearError('scormCourseId', 'scormCourseError')">
                                    <option value="">-- Select Course --</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}" 
                                            {{ old('course_id', $chapter->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="field-error" id="scormCourseError">Please select a Course.</small>
                            </div>

                            {{-- Subject --}}
                            <div class="mb-4">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <select name="subject_id" id="scormChapterId" class="form-control"
                                    onchange="clearError('scormChapterId', 'scormSubjectError')">
                                    <option value="">-- Select Subject --</option>
                                    @if(isset($chapter->subject))
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}" 
                                                {{ old('subject_id', $chapter->subject_id) == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <small class="field-error" id="scormSubjectError">Please select a Subject.</small>
                            </div>

                            {{-- Chapter Name --}}
                            <div class="mb-4">
                                <label class="form-label">Chapter Name <span class="text-danger">*</span></label>
                                <input type="text" name="chapter_name" id="scormChapterName" class="form-control"
                                    placeholder="Enter chapter name" value="{{ old('chapter_name', $chapter->name) }}"
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
                                <label class="form-label">
                                    SCORM Zip File 
                                    <span class="text-muted">(Optional - Upload only if you want to replace)</span>
                                </label>
                                <input type="file" name="zip_file" id="scormZipFile" class="form-control" accept=".zip"
                                    onchange="onZipFileChange(this)">
                                <small class="text-muted">Leave empty to keep existing SCORM package. Upload new ZIP to replace.</small>
                                <div id="zipFileInfo" class="text-muted mt-2">
                                    Current package folder: <strong>{{ $chapter->folder_name }}</strong>
                                </div>
                                <small class="field-error" id="scormZipError">Please upload a valid SCORM Zip file.</small>
                                @if ($errors->has('zip_file'))
                                    <div class="alert alert-danger mt-1" id="scormZipFileError">{{ $errors->first('zip_file') }}</div>
                                    <script>
                                        setTimeout(function() {
                                            const el = document.getElementById('scormZipFileError');
                                            if (el) { el.style.transition='opacity 0.5s'; el.style.opacity='0'; setTimeout(()=>el.remove(),500); }
                                        }, 5000);
                                    </script>
                                @endif
                            </div>

                            <div class="d-flex" style="gap:10px;">
                                <a href="{{ route('chapters') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back
                                </a>
                                <button id="scormSubmitBtn" type="button" class="btn btn-success"
                                    onclick="validateAndSubmit()">
                                    Update SCORM Chapter <i class="fas fa-save ml-1"></i>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        /* ─── VALIDATION ─── */
        function validateAndSubmit() {
            let isValid = true;

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

            // ZIP file is optional in edit mode, so no validation needed

            if (isValid) {
                document.getElementById('scormSubmitBtn').disabled = true;
                document.getElementById('loaderOverlay').style.display = 'flex';
                document.getElementById('scormEditForm').submit();
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

        function onZipFileChange(input) {
            const infoEl = document.getElementById('zipFileInfo');
            const file = input.files[0];
            if (file) {
                infoEl.innerHTML = 'Selected file: <strong>' + file.name + '</strong>';
            } else {
                infoEl.innerHTML = 'Current package folder: <strong>{{ $chapter->folder_name }}</strong>';
            }
            clearError('scormZipFile', 'scormZipError');
        }

        /* ─── DOM READY ─── */
        document.addEventListener('DOMContentLoaded', function () {
            const courseSelect = document.getElementById('scormCourseId');
            const subjectSelect = document.getElementById('scormChapterId');
            const initialCourseId = '{{ old('course_id', $chapter->course_id ?? '') }}';
            const initialSubjectId = '{{ old('subject_id', $chapter->subject_id ?? '') }}';

            const selectedCourseTitle = document.getElementById('selectedCourseTitle');
            const selectedSubjectName = document.getElementById('selectedSubjectName');

            courseSelect.addEventListener('change', function () {
                const courseId = this.value;
                if (courseId) {
                    fetchSubjects(courseId, subjectSelect, null);
                } else {
                    resetSubject(subjectSelect);
                }
                selectedCourseTitle.textContent = courseSelect.options[courseSelect.selectedIndex].text || '-- Select Course --';
                selectedSubjectName.textContent = '-- Select Subject --';
            });

            subjectSelect.addEventListener('change', function () {
                selectedSubjectName.textContent = subjectSelect.options[subjectSelect.selectedIndex].text || '-- Select Subject --';
            });

            if (initialCourseId) {
                fetchSubjects(initialCourseId, subjectSelect, initialSubjectId);
            }
        });

        function resetSubject(selectEl) {
            selectEl.innerHTML = '<option value="">-- Select Subject --</option>';
            selectEl.disabled  = true;
        }

        function fetchSubjects(courseId, selectElement, selectedSubjectId = null) {
            const currentSubjectId = selectedSubjectId || {{ $chapter->subject_id ?? 'null' }};
            
            fetch(`/api/subjects/${courseId}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network error');
                    return res.json();
                })
                .then(data => {
                    let html = '<option value="">-- Select Subject --</option>';
                    if (data.subjects && data.subjects.length > 0) {
                        data.subjects.forEach(s => {
                            const selected = s.id == currentSubjectId ? 'selected' : '';
                            html += `<option value="${s.id}" ${selected}>${s.name}</option>`;
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