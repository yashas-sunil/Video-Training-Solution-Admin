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
                <div class="col-md-4">
                    <label><b>Course</b></label>
                    <select id="course_id" class="form-control">
                        <option value="">Select Course</option>
                        @foreach($courses->sortBy('title') as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Subject -->
                <div class="col-md-4">
                    <label><b>Subject</b></label>
                    <select id="subject_id" class="form-control">
                        <option value="">Select Subject</option>
                    </select>
                </div>

                <!-- Question Bank -->
                <div class="col-md-4">
                    <label><b>Question Bank</b></label>
                    <select id="bank_id" class="form-control">
                        <option value="">Select Bank</option>
                        @foreach($banks->sortBy('name') as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <br>

            <!-- Filter & Assign Buttons -->
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

    // { question_id: 'easy' / 'medium' / 'hard' }
    let selectedQuestions = {};
    let selectAllGlobal   = false;


    document.getElementById('course_id').addEventListener('change', function () {
        let course_id       = this.value;
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


    function filterQuestions(page = 1) {

        let course_id  = document.getElementById('course_id').value;
        let subject_id = document.getElementById('subject_id').value;
        let bank_id    = document.getElementById('bank_id').value;

        if (course_id === "") {
            alert("Please select Course");
            return;
        }

        if (subject_id === "") {
            alert("Please select Subject");
            return;
        }

        if (bank_id === "") {
            alert("Please select Question Bank");
            return;
        }

        fetch(`{{ route('course.questions.filter') }}?course_id=${course_id}&subject_id=${subject_id}&bank_id=${bank_id}&page=${page}`)
            .then(res => res.text())
            .then(html => {

                document.getElementById('questions-container').style.display = 'block';
                document.getElementById('question-list').innerHTML = html;

                document.querySelectorAll('.question-checkbox').forEach(cb => {
                    let id         = cb.value;
                    let difficulty = cb.getAttribute('data-difficulty') || '';

                    if (selectedQuestions[id] !== undefined) {
                        cb.checked = true;
                        selectedQuestions[id] = difficulty;
                    }

                    if (selectAllGlobal) {
                        cb.checked = true;
                        selectedQuestions[id] = difficulty;
                    }
                });

                let selectAll = document.getElementById('selectAll');
                if (selectAll) {
                    selectAll.checked = selectAllGlobal;

                    let newSelectAll = selectAll.cloneNode(true);
                    selectAll.parentNode.replaceChild(newSelectAll, selectAll);

                    newSelectAll.addEventListener('change', function () {
                        selectAllGlobal = this.checked;

                        document.querySelectorAll('.question-checkbox').forEach(cb => {
                            let id         = cb.value;
                            let difficulty = cb.getAttribute('data-difficulty') || '';
                            cb.checked     = this.checked;

                            if (this.checked) {
                                selectedQuestions[id] = difficulty;
                            } else {
                                delete selectedQuestions[id];
                            }
                        });

                        updateSelectedCounts();
                    });
                }

                let searchInput = document.getElementById('questionsSearchInput');
                if (searchInput) {
                    let newSearch = searchInput.cloneNode(true);
                    searchInput.parentNode.replaceChild(newSearch, searchInput);
                    newSearch.addEventListener('keyup', filterQuestionsTable);
                }

                let difficultyFilter = document.getElementById('difficultyFilter');
                if (difficultyFilter) {
                    let newFilter = difficultyFilter.cloneNode(true);
                    difficultyFilter.parentNode.replaceChild(newFilter, difficultyFilter);
                    newFilter.addEventListener('change', filterQuestionsTable);
                }

                updateSelectedCounts();
            });
    }


    function filterQuestionsTable() {
        let searchText       = document.getElementById('questionsSearchInput')?.value.toLowerCase() || '';
        let difficultyFilter = document.getElementById('difficultyFilter')?.value.toLowerCase() || '';

        document.querySelectorAll('#questionsBody tr').forEach(row => {
            let questionText   = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
            let cb             = row.querySelector('.question-checkbox');
            let rowDifficulty  = cb ? (cb.getAttribute('data-difficulty') || '').toLowerCase() : '';

            let searchMatch     = questionText.includes(searchText);
            let difficultyMatch = !difficultyFilter || rowDifficulty === difficultyFilter;

            row.style.display = (searchMatch && difficultyMatch) ? '' : 'none';
        });

        let selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = false;
        selectAllGlobal = false;
    }


    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('question-checkbox')) {
            let cb         = e.target;
            let id         = cb.value;
            let difficulty = cb.getAttribute('data-difficulty') || '';

            if (cb.checked) {
                selectedQuestions[id] = difficulty;
            } else {
                delete selectedQuestions[id];
                selectAllGlobal = false;
            }

            updateSelectedCounts();

            let totalVisible    = 0;
            let selectedVisible = 0;

            document.querySelectorAll('#questionsBody tr').forEach(row => {
                if (row.style.display !== 'none') {
                    totalVisible++;
                    if (row.querySelector('.question-checkbox:checked')) selectedVisible++;
                }
            });

            let selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.checked = (totalVisible > 0 && totalVisible === selectedVisible);
            }
        }
    });


    document.addEventListener('click', function (e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            let url  = e.target.closest('a').getAttribute('href');
            let page = url.split('page=')[1];
            filterQuestions(page);
        }
    });


    function assignQuestions() {

        let ids = Object.keys(selectedQuestions);

        if (ids.length === 0) {
            alert("Please select at least one question");
            return;
        }

        let course_id  = document.getElementById('course_id').value;
        let subject_id = document.getElementById('subject_id').value;

        if (course_id === "") {
            alert("Please select Course");
            return;
        }

        if (subject_id === "") {
            alert("Please select Subject");
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
                question_ids: ids
            })
        })
        .then(res => res.json())
        .then(data => {
            alert("Questions Assigned Successfully");
            selectedQuestions = {};
            selectAllGlobal   = false;
            filterQuestions();
        });
    }


    function updateSelectedCounts() {
        let totalCount  = 0;
        let easyCount   = 0;
        let mediumCount = 0;
        let hardCount   = 0;

        Object.values(selectedQuestions).forEach(difficulty => {
            totalCount++;
            let d = (difficulty || '').toLowerCase().trim();
            if      (d === 'easy')   easyCount++;
            else if (d === 'medium') mediumCount++;
            else if (d === 'hard')   hardCount++;
        });

        document.getElementById('selected_total').value  = totalCount;
        document.getElementById('selected_easy').value   = easyCount;
        document.getElementById('selected_medium').value = mediumCount;
        document.getElementById('selected_hard').value   = hardCount;
    }

</script>
@endsection