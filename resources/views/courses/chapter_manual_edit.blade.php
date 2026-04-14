@extends('adminlte::page')

@section('title', 'Edit Chapter Manual')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Chapter Manual</h1>
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
    .spinner-border { width: 3rem; height: 3rem; }

    .custom-card {
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .form-label { font-weight: 600; }

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

    .remove-lesson-btn { padding: 5px 10px; font-size: 12px; }

    .content-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .file-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .file-row:last-child { border-bottom: 0; }

    .file-meta { font-size: 13px; color: #666; }

    .badge-pill { border-radius: 999px; padding: 5px 10px; font-size: 12px; }

    /* Dropdown row */
    .lesson-jump-row {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .lesson-jump-row .jump-select { min-width: 280px; max-width: 520px; }

    /* lessons default hidden */
    .lesson-card.lesson-hidden { display: none !important; }

    .lesson-highlight {
        transition: box-shadow 0.3s ease;
        box-shadow: 0 0 0 4px rgba(0,123,255,0.25);
    }

    /* Validation error styling */
    .lesson-card.has-error {
        border: 2px solid #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15);
    }

    .validation-message {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
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
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <b>Errors:</b>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        $chapterContents = $chapterContents ?? collect();
                        $lessons = $chapter->lessons ?? collect();
                        $lessonContents = $lessonContents ?? collect();

                        $lessonTypesMap = [
                            'Detailed Trainer Slides' => 'fa-file-powerpoint',
                            'Summary Slides' => 'fa-file-pdf',
                            'Videos' => 'fa-video',
                        ];
                    @endphp

                    {{-- MAIN UPDATE FORM --}}
                    <form method="POST"
                          action="{{ route('chapter.manual.update', $chapter->id) }}"
                          enctype="multipart/form-data"
                          id="editManualForm">
                        @csrf

                        {{-- Chapter Name --}}
                        <div class="mb-4">
                            <label class="form-label">Chapter Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="chapter_name"
                                   class="form-control"
                                   value="{{ old('chapter_name', $chapter->name) }}"
                                   placeholder="Enter chapter name"
                                   required>
                        </div>

                        {{-- Chapter-Level Content (always visible) --}}
                        <div class="content-section">
                            <h5 class="mb-3">
                                <i class="fas fa-book"></i> Chapter-Level Content
                                <small class="text-muted">(Upload more files for chapter)</small>
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
                                        <input type="file"
                                               class="form-control"
                                               name="manual_{{ $type['key'] }}[]"
                                               accept="*/*"
                                               multiple>
                                        <small class="text-muted">Optional · Multiple files allowed</small>
                                    </div>
                                @endforeach
                            </div>

                            <hr>
                            <h6 class="mb-2"><i class="fas fa-folder-open"></i> Existing Chapter Files</h6>

                            @if ($chapterContents->count())
                                @foreach ($chapterContents as $ctype => $items)
                                    <div class="mb-2">
                                        <span class="badge badge-secondary badge-pill">{{ $ctype }}</span>
                                    </div>

                                    @foreach ($items as $c)
                                        <div class="file-row">
                                            <div>
                                                <div><b>{{ $c->original_name ?? basename($c->file_path) }}</b></div>
                                                <div class="file-meta">
                                                    {{ $c->mime_type }} · {{ $c->file_size }}
                                                </div>
                                            </div>

                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="submitDelete('delete-content-{{ $c->id }}', 'Delete this file?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    @endforeach

                                    <div class="mb-3"></div>
                                @endforeach
                            @else
                                <div class="text-muted">No chapter-level files found.</div>
                            @endif
                        </div>

                        {{-- Lessons Section --}}
                        <div class="content-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-1">
                                        <i class="fas fa-graduation-cap"></i> Lessons
                                    </h5>
                                    <small class="text-muted">(Select lesson from dropdown to view)</small>
                                </div>

                                <button type="button" class="btn btn-sm btn-primary" onclick="addLesson()">
                                    <i class="fas fa-plus"></i> Add Lesson
                                </button>
                            </div>

                            {{-- Dropdown --}}
                            <div class="mb-3">
                                <div class="lesson-jump-row">
                                    <div class="jump-select">
                                        <label class="form-label">
                                            <i class="fas fa-list"></i> Select Lesson (Only selected lesson will show)
                                        </label>

                                        <select id="lessonJumpSelect" class="form-control">
                                            <option value="">-- Select Lesson --</option>
                                            @foreach ($lessons as $idx => $lesson)
                                                <option value="lesson-existing-{{ $lesson->id }}">
                                                    Lesson {{ $idx + 1 }} - {{ $lesson->lesson_name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <small class="text-muted">
                                            Select one lesson → only that lesson block will appear below.
                                        </small>
                                    </div>

                                    <div>
                                        <button type="button" class="btn btn-outline-secondary" onclick="showNoLesson()">
                                            <i class="fas fa-eye-slash"></i> Hide Lessons
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="lessonsContainer">
                                @foreach ($lessons as $idx => $lesson)
                                    @php
                                        $lItems = $lessonContents->get($lesson->id) ?? collect();
                                    @endphp

                                    <div class="lesson-card lesson-hidden"
                                         id="lesson-existing-{{ $lesson->id }}"
                                         data-existing="1"
                                         data-has-files="1">

                                        <div class="lesson-header">
                                            <h6 class="mb-0">
                                                <i class="fas fa-chalkboard-teacher"></i> Lesson {{ $idx + 1 }}
                                            </h6>

                                            <button type="button"
                                                    class="btn btn-sm btn-danger remove-lesson-btn"
                                                    onclick="submitDelete('delete-lesson-{{ $lesson->id }}', 'Delete this lesson and all its files?')">
                                                <i class="fas fa-trash"></i> Delete Lesson
                                            </button>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Lesson Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="lessons[{{ $idx }}][lesson_name]"
                                                   class="form-control lesson-name-input"
                                                   value="{{ old("lessons.$idx.lesson_name", $lesson->lesson_name) }}"
                                                   required>

                                            <input type="hidden"
                                                   name="lessons[{{ $idx }}][lesson_id]"
                                                   value="{{ $lesson->id }}">
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-file-powerpoint"></i> Detailed Trainer Slides
                                                </label>
                                                <input type="file"
                                                       class="form-control lesson-file-input"
                                                       name="lessons[{{ $idx }}][manual_detailed_trainer_slides][]"
                                                       accept="*/*" multiple>
                                                <small class="text-muted">Optional · Multiple files</small>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-file-pdf"></i> Summary Slides
                                                </label>
                                                <input type="file"
                                                       class="form-control lesson-file-input"
                                                       name="lessons[{{ $idx }}][manual_summary_slides][]"
                                                       accept="*/*" multiple>
                                                <small class="text-muted">Optional · Multiple files</small>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-video"></i> Videos
                                                </label>
                                                <input type="file"
                                                       class="form-control lesson-file-input"
                                                       name="lessons[{{ $idx }}][manual_videos][]"
                                                       accept="video/*" multiple>
                                                <small class="text-muted">Optional · Multiple files</small>
                                            </div>
                                        </div>

                                        <hr>
                                        <h6 class="mb-2"><i class="fas fa-folder-open"></i> Existing Lesson Files</h6>

                                        @if ($lItems->count())
                                            @foreach ($lItems->groupBy('content_type') as $type => $items)
                                                <div class="mb-2">
                                                    <span class="badge badge-info badge-pill">
                                                        <i class="fas {{ $lessonTypesMap[$type] ?? 'fa-file' }}"></i>
                                                        {{ $type }}
                                                    </span>
                                                </div>

                                                @foreach ($items as $c)
                                                    <div class="file-row">
                                                        <div>
                                                            <div><b>{{ $c->original_name ?? basename($c->file_path) }}</b></div>
                                                            <div class="file-meta">
                                                                {{ $c->mime_type }} · {{ $c->file_size }}
                                                            </div>
                                                        </div>

                                                        <button type="button"
                                                                class="btn btn-sm btn-danger"
                                                                onclick="submitDelete('delete-content-{{ $c->id }}', 'Delete this file?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </div>
                                                @endforeach

                                                <div class="mb-3"></div>
                                            @endforeach
                                        @else
                                            <div class="text-muted">No files found for this lesson.</div>
                                        @endif

                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex mt-4" style="gap:10px;">
                            <a href="{{ route('chapters') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>

                            <button id="updateManualBtn" type="button" class="btn btn-success">
                                Update Manual <i class="fas fa-save ml-1"></i>
                            </button>
                        </div>
                    </form>

                    {{-- Hidden delete forms OUTSIDE main form --}}
                    @if ($chapterContents->count())
                        @foreach ($chapterContents as $ctype => $items)
                            @foreach ($items as $c)
                                <form id="delete-content-{{ $c->id }}"
                                      method="POST"
                                      action="{{ route('chapter.manual.content.delete', $c->id) }}"
                                      style="display:none;">
                                    @csrf
                                </form>
                            @endforeach
                        @endforeach
                    @endif

                    @if ($lessonContents && $lessonContents->count())
                        @foreach ($lessonContents as $lessonId => $items)
                            @foreach ($items as $c)
                                <form id="delete-content-{{ $c->id }}"
                                      method="POST"
                                      action="{{ route('chapter.manual.content.delete', $c->id) }}"
                                      style="display:none;">
                                    @csrf
                                </form>
                            @endforeach
                        @endforeach
                    @endif

                    @if ($lessons->count())
                        @foreach ($lessons as $lesson)
                            <form id="delete-lesson-{{ $lesson->id }}"
                                  method="POST"
                                  action="{{ route('chapter.manual.lesson.delete', $lesson->id) }}"
                                  style="display:none;">
                                @csrf
                            </form>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let lessonCount = 0;
    const loader = document.getElementById('loaderOverlay');

    function submitDelete(formId, msg) {
        if (!confirm(msg || 'Are you sure?')) return;

        const f = document.getElementById(formId);
        if (!f) {
            alert('Delete form not found: ' + formId);
            return;
        }

        loader.style.display = 'flex';
        f.submit();
    }

    function hideAllLessons() {
        const cards = document.querySelectorAll('#lessonsContainer .lesson-card');
        cards.forEach(c => {
            c.classList.add('lesson-hidden');
            c.classList.remove('lesson-highlight');
        });
    }

    function showOnlyLesson(lessonId) {
        hideAllLessons();
        const el = document.getElementById(lessonId);
        if (!el) return;

        el.classList.remove('lesson-hidden');
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });

        el.classList.add('lesson-highlight');
        setTimeout(() => el.classList.remove('lesson-highlight'), 800);
    }

    function showNoLesson() {
        const sel = document.getElementById('lessonJumpSelect');
        if (sel) sel.value = "";
        hideAllLessons();
    }

    document.addEventListener('DOMContentLoaded', function () {
        hideAllLessons();

        const sel = document.getElementById('lessonJumpSelect');
        if (sel) {
            sel.addEventListener('change', function () {
                const targetId = this.value;
                if (!targetId) {
                    hideAllLessons();
                    return;
                }
                showOnlyLesson(targetId);
            });
        }

        attachFileInputListeners();
    });

    function getCurrentLessonIndexCount() {
        const container = document.getElementById('lessonsContainer');
        return container.querySelectorAll('.lesson-card').length;
    }

    function getLastNewLesson() {
        const container = document.getElementById('lessonsContainer');
        const cards = Array.from(container.querySelectorAll('.lesson-card'));
                for (let i = cards.length - 1; i >= 0; i--) {
            const card = cards[i];
            if (card.id.startsWith('lesson-new-')) {
                return card;
            }
        }
        return null;
    }

    function hasFilesInLesson(lessonCard) {
        if (lessonCard.dataset.existing === "1" && lessonCard.dataset.hasFiles === "1") {
            return true;
        }

        const fileInputs = lessonCard.querySelectorAll('input[type="file"].lesson-file-input');
        
        for (let input of fileInputs) {
            if (input.files && input.files.length > 0) {
                return true;
            }
        }
        
        return false;
    }

    function attachFileInputListeners() {
        const container = document.getElementById('lessonsContainer');
        
        container.addEventListener('change', function(e) {
            if (e.target.matches('input[type="file"].lesson-file-input')) {
                const lessonCard = e.target.closest('.lesson-card');
                if (lessonCard) {
                    // File upload hone par error message remove karo
                    const errorMsg = lessonCard.querySelector('.validation-message');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                    lessonCard.classList.remove('has-error');
                }
            }
        });
    }

    function showValidationError(lessonCard, message) {
        const existingError = lessonCard.querySelector('.validation-message');
        if (existingError) {
            existingError.remove();
        }

        lessonCard.classList.add('has-error');

        const errorHTML = `
            <div class="validation-message">
                <i class="fas fa-exclamation-triangle"></i>
                <span>${message}</span>
            </div>
        `;

        const header = lessonCard.querySelector('.lesson-header');
        header.insertAdjacentHTML('afterend', errorHTML);

        lessonCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function addLesson() {
        const lastNewLesson = getLastNewLesson();
        
        if (lastNewLesson) {
            if (!hasFilesInLesson(lastNewLesson)) {
                const lessonNumber = Array.from(document.querySelectorAll('.lesson-card')).indexOf(lastNewLesson) + 1;
                
                showValidationError(
                    lastNewLesson,
                    `⚠️ Please upload at least ONE file in Lesson ${lessonNumber} before adding a new lesson!`
                );

                hideAllLessons();
                lastNewLesson.classList.remove('lesson-hidden');
                
                const dropdown = document.getElementById('lessonJumpSelect');
                if (dropdown) {
                    dropdown.value = lastNewLesson.id;
                }

                return; 
            }
        }

        lessonCount++;

        const container = document.getElementById('lessonsContainer');
        const currentIndex = getCurrentLessonIndexCount();

        const lessonHTML = `
            <div class="lesson-card" id="lesson-new-${lessonCount}" data-existing="0" data-has-files="0">
                <div class="lesson-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chalkboard-teacher"></i> Lesson ${currentIndex + 1} (New)
                    </h6>
                    <button type="button" class="btn btn-sm btn-danger remove-lesson-btn"
                            onclick="removeNewLesson(${lessonCount})">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lesson Name <span class="text-danger">*</span></label>
                    <input type="text" name="lessons[${currentIndex}][lesson_name]"
                           class="form-control lesson-name-input"
                           placeholder="e.g., New Lesson Name" required>
                    <input type="hidden" name="lessons[${currentIndex}][lesson_id]" value="">
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="fas fa-file-powerpoint"></i> Detailed Trainer Slides
                        </label>
                        <input type="file" class="form-control lesson-file-input"
                               name="lessons[${currentIndex}][manual_detailed_trainer_slides][]"
                               accept="*/*" multiple>
                        <small class="text-muted">Optional · Multiple files</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="fas fa-file-pdf"></i> Summary Slides
                        </label>
                        <input type="file" class="form-control lesson-file-input"
                               name="lessons[${currentIndex}][manual_summary_slides][]"
                               accept="*/*" multiple>
                        <small class="text-muted">Optional · Multiple files</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="fas fa-video"></i> Videos
                        </label>
                        <input type="file" class="form-control lesson-file-input"
                               name="lessons[${currentIndex}][manual_videos][]"
                               accept="video/*" multiple>
                        <small class="text-muted">Optional · Multiple files</small>
                    </div>
                </div>
            </div>
        `;

        hideAllLessons();
        container.insertAdjacentHTML('beforeend', lessonHTML);

        setTimeout(() => {
            showOnlyLesson(`lesson-new-${lessonCount}`);
        }, 50);

        updateLessonDropdown();
    }

    function removeNewLesson(id) {
        const el = document.getElementById(`lesson-new-${id}`);
        if (el && confirm('Remove this new lesson block?')) {
            el.remove();
            reindexLessons();
            updateLessonDropdown();
            hideAllLessons();
        }
    }

    function reindexLessons() {
        const container = document.getElementById('lessonsContainer');
        const cards = container.querySelectorAll('.lesson-card');

        cards.forEach((card, index) => {
            const header = card.querySelector('.lesson-header h6');
            if (header) {
                const isNew = card.id.startsWith('lesson-new-');
                header.innerHTML = `<i class="fas fa-chalkboard-teacher"></i> Lesson ${index + 1}${isNew ? ' (New)' : ''}`;
            }

            const inputs = card.querySelectorAll('input[name^="lessons"], select[name^="lessons"], textarea[name^="lessons"]');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                const newName = name.replace(/lessons\[\d+\]/, `lessons[${index}]`);
                input.setAttribute('name', newName);
            });
        });
    }

    function updateLessonDropdown() {
        const container = document.getElementById('lessonsContainer');
        const cards = container.querySelectorAll('.lesson-card');
        const dropdown = document.getElementById('lessonJumpSelect');
        
        if (!dropdown) return;

        dropdown.innerHTML = '<option value="">-- Select Lesson --</option>';

        cards.forEach((card, index) => {
            const lessonNameInput = card.querySelector('.lesson-name-input');
            const lessonName = lessonNameInput ? lessonNameInput.value : 'New Lesson';
            const isNew = card.id.startsWith('lesson-new-');
            
            const option = document.createElement('option');
            option.value = card.id;
            option.textContent = `Lesson ${index + 1} - ${lessonName}${isNew ? ' (New)' : ''}`;
            dropdown.appendChild(option);
        });
    }

    document.getElementById('updateManualBtn').addEventListener('click', function() {
        const lastNewLesson = getLastNewLesson();
        
        if (lastNewLesson && !hasFilesInLesson(lastNewLesson)) {
            const lessonNumber = Array.from(document.querySelectorAll('.lesson-card')).indexOf(lastNewLesson) + 1;
            
            showValidationError(
                lastNewLesson,
                `⚠️ Please upload at least ONE file in Lesson ${lessonNumber} before submitting!`
            );

            hideAllLessons();
            lastNewLesson.classList.remove('lesson-hidden');
            
            const dropdown = document.getElementById('lessonJumpSelect');
            if (dropdown) {
                dropdown.value = lastNewLesson.id;
            }

            return; 
        }

        reindexLessons();
        this.disabled = true;
        loader.style.display = 'flex';
        document.getElementById('editManualForm').submit();
    });

    document.addEventListener('DOMContentLoaded', function() {
        updateLessonDropdown();
    });
</script>

@endsection