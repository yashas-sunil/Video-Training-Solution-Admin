@extends('adminlte::page')

{{-- @push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
@endpush --}}

@push('js')
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endpush
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- <style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        min-height: 38px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        border-color: #0056b3;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        margin: 5px 5px 5px 0;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 8px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #fff;
    }
</style> --}}
@endsection

@section('title', 'Admin Test creation')

@section('content_header')
    <div class="row">
        <div class="col">
            <h1 class="m-0 text-dark">Create Admin Test</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="adminTestForm" action="{{ route('admin-test.store') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        
                        {{-- Test Name --}}
                        <div class="form-group row">
                            <label for="test_name" class="col-md-2 col-form-label">Test Name <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="text" class="form-control @error('test_name') is-invalid @enderror" 
                                    id="test_name" name="test_name" placeholder="Enter test name" 
                                    value="{{ old('test_name') }}" required>
                                @error('test_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Course Selection --}}
                        <div class="form-group row">
                            <label for="course_id" class="col-md-2 col-form-label">Course <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select class="form-control @error('course_id') is-invalid @enderror" 
                                    id="course_id" name="course_id" required>
                                    <option value="">-Select Course-</option>
                                        @foreach($course->sortBy('title') as $courses)
                                            <option value="{{ $courses->id }}" {{ old('course_id') == $courses->id ? 'selected' : '' }}>
                                                {{ $courses->title }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Subject Selection --}}
                        <div class="form-group row">
                            <label for="subject_ids" class="col-md-2 col-form-label">Subject <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <div style="margin-bottom: 10px;">
                                    <button type="button" id="selectAllSubjectsBtn" class="btn btn-sm btn-info" style="display: none;">
                                        <i class="fas fa-check-double"></i> Select All
                                    </button>
                                    <button type="button" id="clearAllSubjectsBtn" class="btn btn-sm btn-warning" style="display: none;">
                                        <i class="fas fa-times"></i> Clear All
                                    </button>
                                </div>
                                <select class="form-control select2 @error('subject_ids') is-invalid @enderror" 
                                    id="subject_ids" name="subject_ids[]" multiple="multiple" required>
                                    <option value="">-- Select Subjects --</option>
                                </select>
                                @error('subject_ids')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Questions Selection --}}
                        <div class="form-group row">
                            <label for="questions_section" class="col-md-2 col-form-label">Select Questions <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <div id="questions_section" style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto; display: none;">
                                    <div id="questionsLoading" style="text-align: center; color: #999;">
                                        <p>Select course and subject to view questions</p>
                                    </div>
                                    <div id="questionsList" style="display: none;">
                                        <div style="margin-bottom: 15px; display: flex; gap: 10px;">
                                            <input type="text" id="questionsSearchInput" class="form-control" placeholder="Search questions by text..." style="flex: 1;">
                                            <select id="difficultyFilter" class="form-control" style="width: 150px;">
                                                <option value="">All Difficulties</option>
                                                <option value="easy">Easy</option>
                                                <option value="medium">Medium</option>
                                                <option value="hard">Hard</option>
                                            </select>
                                        </div>
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px;">
                                                        <input type="checkbox" id="selectAllQuestions" title="Select all questions">
                                                    </th>
                                                    <th>Question</th>
                                                    <th style="width: 100px;">Difficulty</th>
                                                </tr>
                                            </thead>
                                            <tbody id="questionsBody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Selected: <span id="selectedQuestionsCount">0</span> questions</small>
                            </div>
                        </div>

                        {{-- Total Questions Count --}}
                        <div class="form-group row">
                            <label for="total_ques_count" class="col-md-2 col-form-label">Total Questions</label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" 
                                    id="total_ques_count" name="total_ques_count" 
                                    value="0" min="0" disabled readonly>
                            </div>
                        </div>

                        {{-- Easy Count --}}
                        <div class="form-group row">
                            <label for="easy_count" class="col-md-2 col-form-label">Easy Questions</label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" 
                                    id="easy_count" name="easy_count" 
                                    value="0" min="0" disabled readonly>
                            </div>
                        </div>

                        {{-- Medium Count --}}
                        <div class="form-group row">
                            <label for="medium_count" class="col-md-2 col-form-label">Medium Questions</label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" 
                                    id="medium_count" name="medium_count" 
                                    value="0" min="0" disabled readonly>
                            </div>
                        </div>

                        {{-- Hard Count --}}
                        <div class="form-group row">
                            <label for="hard_count" class="col-md-2 col-form-label">Hard Questions</label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" 
                                    id="hard_count" name="hard_count" 
                                    value="0" min="0" disabled readonly>
                            </div>
                        </div>

                        {{-- Status --}}
                        {{-- <div class="form-group row">
                            <label for="status" class="col-md-2 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                    <option value="">-- Select Status --</option>
                                    <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}

                        {{-- Buttons --}}
                        <div class="form-group row">
                            <div class="col-md-10 offset-md-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Save
                                </button>
                                <a href="{{ route('admin-test') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function () {
            // Initialize Select2 for subjects
            $('#subject_ids').select2({
                placeholder: '-- Select Subjects --',
                allowClear: false,
                width: '100%',
                dropdownParent: $('#subject_ids').closest('.col-md-10')
            });

            // Load subjects when course changes
            $('#course_id').on('change', function () {
                let courseId = $(this).val();
                $('#subject_ids').empty().append('<option value="">-- Select Subjects --</option>');
                $('#questions_section').hide();
                $('#questionsList').hide();
                $('#questionsLoading').show();
                $('#selectAllSubjectsBtn').hide();
                $('#clearAllSubjectsBtn').hide();
                
                if (courseId) {
                    $.ajax({
                        url: '{{ url('/api/subjects') }}' + '/' + courseId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            if (data.subjects && data.subjects.length > 0) {
                                $.each(data.subjects, function (key, subject) {
                                    $('#subject_ids').append('<option value="' + subject.id + '">' + subject.name + '</option>');
                                });
                                // Show Select All and Clear All buttons
                                $('#selectAllSubjectsBtn').show();
                                $('#clearAllSubjectsBtn').show();
                            }
                        }
                    });
                }
            });

            // Select All Subjects button click
            $('#selectAllSubjectsBtn').on('click', function(e) {
                e.preventDefault();
                // Get all option values except the empty one
                let allValues = $('#subject_ids').find('option:not(:first)').map(function() {
                    return $(this).val();
                }).get();
                
                $('#subject_ids').val(allValues).trigger('change');
            });

            // Clear All Subjects button click
            $('#clearAllSubjectsBtn').on('click', function(e) {
                e.preventDefault();
                $('#subject_ids').val(null).trigger('change');
                $('#subject_ids').find('option:first').prop('selected', false);
            });

            // Load questions when subject changes
            $('#subject_ids').on('change', function () {
                loadQuestions();
            });

            // Function to load questions
            function loadQuestions() {
                let courseId = $('#course_id').val();
                let selectedSubjects = $('#subject_ids').val();

                // Clear previous selections and reset counts
                $('.question-checkbox').prop('checked', false);
                $('#selectAllQuestions').prop('checked', false);
                $('#questionsSearchInput').val(''); // Clear search input
                $('#difficultyFilter').val(''); // Clear difficulty filter
                updateSelectedCount();

                if (!courseId || !selectedSubjects || selectedSubjects.length === 0) {
                    $('#questions_section').hide();
                    $('#questionsList').hide();
                    $('#questionsLoading').text('Select course and subject to view questions').show();
                    return;
                }

                $('#questionsLoading').text('Loading questions...').show();
                $('#questionsList').hide();

                $.ajax({
                    url: '{{ url('/api/questions-by-course-subject') }}',
                    type: 'GET',
                    data: {
                        course_id: courseId,
                        subject_ids: selectedSubjects.join(',')
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.questions && data.questions.length > 0) {
                            // Sort questions by difficulty level
                            let sortedQuestions = data.questions.sort(function(a, b) {
                                let difficultyOrder = { 'easy': 1, 'medium': 2, 'hard': 3 };
                                let diffA = (a.difficult_level_name || 'Easy').toLowerCase().trim();
                                let diffB = (b.difficult_level_name || 'Easy').toLowerCase().trim();
                                return (difficultyOrder[diffA] || 1) - (difficultyOrder[diffB] || 1);
                            });

                            let tbody = '';
                            // Store questions data globally for easy access
                            window.questionsData = {};
                            $.each(sortedQuestions, function (key, question) {
                                let difficulty = question.difficult_level_name || 'Easy';
                                window.questionsData[question.id] = {
                                    id: question.id,
                                    text: question.question_text,
                                    difficulty: difficulty
                                };
                                tbody += '<tr>' +
                                    '<td><input type="checkbox" class="question-checkbox" value="' + question.id + '" data-difficulty="' + difficulty + '"></td>' +
                                    '<td>' + question.question_text + '</td>' +
                                    '<td>' + difficulty + '</td>' +
                                    '</tr>';
                            });
                            $('#questionsBody').html(tbody);
                            $('#questionsList').show();
                            $('#questionsLoading').hide();
                            $('#questions_section').show();
                        } else {
                            $('#questionsLoading').text('No questions found for selected criteria').show();
                            $('#questionsList').hide();
                            $('#questions_section').show();
                        }
                    },
                    error: function () {
                        $('#questionsLoading').text('Error loading questions. Please try again.').show();
                        $('#questionsList').hide();
                        $('#questions_section').show();
                    }
                });
            }

            // Select all questions checkbox
            $(document).on('change', '#selectAllQuestions', function () {
                let isChecked = $(this).is(':checked');
                // Only select visible checkboxes (filtered by difficulty/search)
                $('#questionsBody tr:visible .question-checkbox').prop('checked', isChecked);
                updateSelectedCount();
            });

            // Filter questions by search and difficulty
            function filterQuestions() {
                let searchText = $('#questionsSearchInput').val().toLowerCase();
                let difficultyFilter = $('#difficultyFilter').val().toLowerCase();
                
                $('#questionsBody tr').each(function () {
                    let questionText = $(this).find('td:eq(1)').text().toLowerCase();
                    let difficulty = $(this).find('td:eq(2)').text().toLowerCase().trim();
                    
                    let searchMatch = questionText.includes(searchText);
                    let difficultyMatch = !difficultyFilter || difficulty.includes(difficultyFilter);
                    
                    if (searchMatch && difficultyMatch) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            // Search questions functionality
            $(document).on('keyup', '#questionsSearchInput', function () {
                filterQuestions();
            });

            // Filter by difficulty
            $(document).on('change', '#difficultyFilter', function () {
                filterQuestions();
            });

            // Update count when individual questions are selected
            $(document).on('change', '.question-checkbox', function () {
                updateSelectedCount();
                
                // Update select all checkbox - only count visible questions
                let totalQuestions = $('#questionsBody tr:visible .question-checkbox').length;
                let selectedQuestions = $('#questionsBody tr:visible .question-checkbox:checked').length;
                
                if (totalQuestions > 0 && totalQuestions === selectedQuestions) {
                    $('#selectAllQuestions').prop('checked', true);
                } else {
                    $('#selectAllQuestions').prop('checked', false);
                }
            });

            // Function to update selected count and difficulty counts
            function updateSelectedCount() {
                let selectedCheckboxes = $('.question-checkbox:checked');
                let totalCount = selectedCheckboxes.length;
                let easyCount = 0;
                let mediumCount = 0;
                let hardCount = 0;

                selectedCheckboxes.each(function () {
                    let difficulty = $(this).data('difficulty');
                    if (difficulty) {
                        difficulty = difficulty.toLowerCase().trim();
                        if (difficulty === 'easy') {
                            easyCount++;
                        } else if (difficulty === 'medium') {
                            mediumCount++;
                        } else if (difficulty === 'hard') {
                            hardCount++;
                        }
                    }
                });

                // Update display and hidden fields
                $('#selectedQuestionsCount').text(totalCount);
                $('#total_ques_count').val(totalCount);
                $('#easy_count').val(easyCount);
                $('#medium_count').val(mediumCount);
                $('#hard_count').val(hardCount);
            }

            // Form submission
            $('#adminTestForm').on('submit', function (e) {
                let selectedQuestions = $('.question-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();

                if (selectedQuestions.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one question');
                    return false;
                }

                // Enable disabled fields temporarily for form submission
                $('#total_ques_count').prop('disabled', false);
                $('#easy_count').prop('disabled', false);
                $('#medium_count').prop('disabled', false);
                $('#hard_count').prop('disabled', false);

                // Create hidden input for selected questions if not exists
                if ($('input[name="selected_questions"]').length === 0) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'selected_questions',
                        value: selectedQuestions.join(',')
                    }).appendTo('#adminTestForm');
                } else {
                    $('input[name="selected_questions"]').val(selectedQuestions.join(','));
                }
            });
        });
    </script>
@stop