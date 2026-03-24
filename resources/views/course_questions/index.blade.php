@extends('adminlte::page')

@section('title', 'Assign Questions To Course')

@section('content')

<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header">
            <h4 class="card-title">Assign Questions To Course</h4>
        </div>

        <div class="card-body">

            <!-- Filters Row -->
            <div class="row">

                <!-- Course -->
                <div class="col-md-3">
                    <label><b>Course</b></label>
                    <select id="course_id" class="form-control">
                        <option value="">Select Course</option>
                        @foreach($courses->sortBy('title') as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Subject -->
                <div class="col-md-3">
                    <label><b>Subject</b></label>
                    <select id="subject_id" class="form-control">
                        <option value="">Select Subject</option>
                    </select>
                </div>

                <!-- Question Bank -->
                <div class="col-md-3">
                    <label><b>Question Bank</b></label>
                    <select id="bank_id" class="form-control">
                        <option value="">Select Bank</option>
                        @foreach($banks->sortBy('name') as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Difficulty Level -->
                <div class="col-md-3">
                    <label><b>Difficulty Level</b></label>
                    <select id="difficulty_level" class="form-control">
                        <option value="">Select Level</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <br>

            <!-- Filter Button -->
            <div class="text-right">
                <button class="btn btn-primary" onclick="filterQuestions()">
                    <i class="fas fa-filter"></i> Filter Questions
                </button>
                <button class="btn btn-success ml-2" onclick="assignQuestions()">
                    <i class="fas fa-check"></i> Assign Questions
                </button>
            </div>

            <br>

            <!-- Selected Question Counts -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label><b>Selected Total</b></label>
                    <input type="text" id="selected_total" class="form-control" readonly value="0">
                </div>
                <div class="col-md-3">
                    <label><b>Selected Easy</b></label>
                    <input type="text" id="selected_easy" class="form-control" readonly value="0">
                </div>
                <div class="col-md-3">
                    <label><b>Selected Medium</b></label>
                    <input type="text" id="selected_medium" class="form-control" readonly value="0">
                </div>
                <div class="col-md-3">
                    <label><b>Selected Hard</b></label>
                    <input type="text" id="selected_hard" class="form-control" readonly value="0">
                </div>
            </div>

            <hr>

            <!-- Questions Scroll Container -->
            <div id="questions-container" style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; max-height: 500px; overflow-y: auto; margin-top: 20px; display: none;">
                <div id="question-list"></div>
            </div>

        </div>
    </div>
</div>

@endsection


@section('js')
<script>

    let selectedQuestions = [];
    let selectAllGlobal = false;

    /* COURSE CHANGE -> LOAD SUBJECTS */
    document.getElementById('course_id').addEventListener('change', function () {
        let course_id = this.value;
        let subjectDropdown = document.getElementById('subject_id');

        subjectDropdown.innerHTML = '<option value="">Loading...</option>';

        if (course_id) {
            fetch(`/get-subjects/${course_id}`)
                .then(res => res.json())
                .then(data => {
                    subjectDropdown.innerHTML = '<option value="">Select Subject</option>';
                    data.forEach(subject => {
                        subjectDropdown.innerHTML += `<option value="${subject.id}">${subject.name}</option>`;
                    });
                });
        } else {
            subjectDropdown.innerHTML = '<option value="">Select Subject</option>';
        }
    });


    /* FILTER QUESTIONS (with pagination support) */
    function filterQuestions(page = 1) {

        let course_id     = document.getElementById('course_id').value;
        let subject_id    = document.getElementById('subject_id').value;
        let bank_id       = document.getElementById('bank_id').value;
        let difficulty    = document.getElementById('difficulty_level').value;

        if (course_id === "") {
            alert("Please select course");
            return;
        }

        if (bank_id === "") {
            alert("Please select question bank");
            return;
        }

        fetch(`{{ route('course.questions.filter') }}?course_id=${course_id}&subject_id=${subject_id}&bank_id=${bank_id}&difficulty=${difficulty}&page=${page}`)
            .then(res => res.text())
            .then(data => {

                // Show container
                document.getElementById('questions-container').style.display = 'block';

                // Load HTML into list
                document.getElementById('question-list').innerHTML = data;

                // Restore checkbox states
                document.querySelectorAll('.question-checkbox').forEach(cb => {
                    if (selectAllGlobal) {
                        cb.checked = true;
                        if (!selectedQuestions.includes(cb.value)) {
                            selectedQuestions.push(cb.value);
                        }
                    } else {
                        if (selectedQuestions.includes(cb.value)) {
                            cb.checked = true;
                        }
                    }
                });

                // Handle Select All checkbox
                let selectAll = document.getElementById('selectAll');
                if (selectAll) {
                    selectAll.checked = selectAllGlobal;

                    selectAll.addEventListener('change', function () {
                        selectAllGlobal = this.checked;

                        document.querySelectorAll('#questionsBody tr').forEach(row => {
                            if (row.style.display !== 'none') {
                                let cb = row.querySelector('.question-checkbox');
                                if (cb) {
                                    cb.checked = this.checked;
                                    let id = cb.value;
                                    if (this.checked) {
                                        if (!selectedQuestions.includes(id)) {
                                            selectedQuestions.push(id);
                                        }
                                    } else {
                                        selectedQuestions = selectedQuestions.filter(q => q != id);
                                    }
                                }
                            }
                        });

                        updateSelectedCounts();
                    });
                }

                // Setup search & difficulty filter listeners
                let questionsSearchInput = document.getElementById('questionsSearchInput');
                let difficultyFilter     = document.getElementById('difficultyFilter');

                if (questionsSearchInput) {
                    questionsSearchInput.addEventListener('keyup', filterQuestionsTable);
                }
                if (difficultyFilter) {
                    difficultyFilter.addEventListener('change', filterQuestionsTable);
                }

                updateSelectedCounts();
            });
    }


    /* FILTER QUESTIONS TABLE (client-side search + difficulty) */
    function filterQuestionsTable() {
        let searchText      = document.getElementById('questionsSearchInput')?.value.toLowerCase() || '';
        let difficultyFilter = document.getElementById('difficultyFilter')?.value.toLowerCase() || '';

        document.querySelectorAll('#questionsBody tr').forEach(row => {
            let questionText = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
            let difficulty   = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase().trim() || '';

            let searchMatch     = questionText.includes(searchText);
            let difficultyMatch = !difficultyFilter || difficulty.includes(difficultyFilter);

            row.style.display = (searchMatch && difficultyMatch) ? '' : 'none';
        });

        // Reset select all when filter changes
        let selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = false;
        selectAllGlobal = false;
    }


    /* CHECKBOX TRACKING (delegated) */
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('question-checkbox')) {
            let id = e.target.value;

            if (e.target.checked) {
                if (!selectedQuestions.includes(id)) {
                    selectedQuestions.push(id);
                }
            } else {
                selectedQuestions = selectedQuestions.filter(q => q != id);
                selectAllGlobal   = false;
            }

            updateSelectedCounts();

            // Sync Select All checkbox state
            let totalVisible    = 0;
            let selectedVisible = 0;

            document.querySelectorAll('#questionsBody tr').forEach(row => {
                if (row.style.display !== 'none') {
                    totalVisible++;
                    if (row.querySelector('.question-checkbox:checked')) {
                        selectedVisible++;
                    }
                }
            });

            let selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.checked = (totalVisible > 0 && totalVisible === selectedVisible);
            }
        }
    });


    /* AJAX PAGINATION */
    document.addEventListener('click', function (e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            let url  = e.target.closest('a').getAttribute('href');
            let page = url.split('page=')[1];
            filterQuestions(page);
        }
    });


    /* ASSIGN QUESTIONS */
    function assignQuestions() {

        if (selectedQuestions.length === 0) {
            alert("Please select at least one question");
            return;
        }

        let course_id  = document.getElementById('course_id').value;
        let subject_id = document.getElementById('subject_id').value;

        if (course_id === "") {
            alert("Please select course");
            return;
        }

        if (subject_id === "") {
            alert("Please select subject");
            return;
        }

        fetch(`{{ route('course.questions.assign') }}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                course_id:    course_id,
                subject_id:   subject_id,
                question_ids: selectedQuestions
            })
        })
        .then(res => res.json())
        .then(data => {
            alert("Questions Assigned Successfully");
            selectedQuestions = [];
            selectAllGlobal   = false;
            filterQuestions();
        });
    }


    /* UPDATE SELECTED COUNTS */
    function updateSelectedCounts() {
        let totalCount  = 0;
        let easyCount   = 0;
        let mediumCount = 0;
        let hardCount   = 0;

        document.querySelectorAll('.question-checkbox:checked').forEach(cb => {
            let difficulty = cb.getAttribute('data-difficulty');
            totalCount++;

            if (difficulty === 'easy')        easyCount++;
            else if (difficulty === 'medium') mediumCount++;
            else if (difficulty === 'hard')   hardCount++;
        });

        document.getElementById('selected_total').value  = totalCount;
        document.getElementById('selected_easy').value   = easyCount;
        document.getElementById('selected_medium').value = mediumCount;
        document.getElementById('selected_hard').value   = hardCount;
    }

</script>
@endsection