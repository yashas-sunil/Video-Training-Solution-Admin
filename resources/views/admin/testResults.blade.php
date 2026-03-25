@extends('adminlte::page')

@section('title', 'Admin Test Results')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark">Test Leaderboard</h1>
</div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!-- Filter Section -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ url('test-result') }}" class="form-inline align-items-end" onsubmit="return validateFilters()">

                    <div class="form-group mr-3 mb-0">
                        <label for="filter_type" class="mr-2">Filter By:</label>
                        <div>
                            <select id="filter_type" name="filter_type" class="form-control" onchange="updateFilters()">
                                <option value="">-- Select --</option>
                                <option value="test"   {{ $filterType === 'test'   ? 'selected' : '' }}>Test</option>
                                <option value="course" {{ $filterType === 'course' ? 'selected' : '' }}>Course</option>
                            </select>
                            <small id="filter_type_error" class="text-danger" style="display:none;">
                                Please select a filter type.
                            </small>
                        </div>
                    </div>

                    <div class="form-group mr-3 mb-0">
                        <label for="filter_id" class="mr-2" id="filter_label">Select Test:</label>
                        <div>
                            <select id="filter_id" name="filter_id" class="form-control">
                                <option value="">-- Select --</option>
                                @if($filterType === 'test')
                                    @foreach($tests as $test)
                                        <option value="{{ $test->id }}" {{ $filterId == $test->id ? 'selected' : '' }}>
                                            {{ $test->test_name }}
                                        </option>
                                    @endforeach
                                @elseif($filterType === 'course')
                                    @foreach($packages as $package)
                                        <option value="{{ $package->id }}" {{ $filterId == $package->id ? 'selected' : '' }}>
                                            {{ $package->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small id="filter_id_error" class="text-danger" style="display:none;">
                                Please select a test / course.
                            </small>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">View Leaderboard</button>
                        <a href="{{ url('test-result') }}" class="btn btn-secondary ml-2">Clear Filters</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Leaderboard Section -->
@if(!empty($leaderboard) && count($leaderboard) > 0)
    <div class="card shadow-sm">
        <div class="card-header" style="background:primary; color:rgb(0, 0, 0);">
            <h5 class="m-0">Leaderboard</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead style="background:#f5f5f5;">
                    <tr>
                        <th width="5%"  style="text-align:center;"><strong>Rank</strong></th>
                        <th width="35%"><strong>Student Name</strong></th>
                        @if($filterType === 'course')
                            <th width="15%"><strong>Test Name</strong></th>
                        @endif
                        <th width="15%" style="text-align:center;"><strong>Correct Answers</strong></th>
                        <th width="15%" style="text-align:center;"><strong>Total Questions</strong></th>
                        <th width="15%" style="text-align:center;"><strong>Score %</strong></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaderboard as $index => $result)
                        <tr class="{{ $index === 0 ? 'table-success' : ($index === 1 ? 'table-info' : ($index === 2 ? 'table-warning' : '')) }}">
                            <td style="text-align:center; font-weight:bold; font-size:18px;">
                                {{ $index + 1 }}
                            </td>
                            <td>{{ $result->user_name }}</td>
                            @if($filterType === 'course')
                                <td>{{ $result->test_name }}</td>
                            @endif
                            <td style="text-align:center;">
                                <span class="badge badge-success">{{ $result->correct_answers }}</span>
                            </td>
                            <td style="text-align:center;">{{ $result->total_questions }}</td>
                            <td style="text-align:center;">
                                <strong>{{ $result->percentage }}%</strong>
                                <div class="progress" style="height: 5px; margin-top: 5px;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $result->percentage }}%; background-color: {{ $result->percentage >= 80 ? '#28a745' : ($result->percentage >= 60 ? '#ffc107' : '#dc3545') }}"
                                         aria-valuenow="{{ $result->percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="alert alert-info">
        <strong>No results found.</strong> Please select a filter type and value to view the leaderboard.
    </div>
@endif

@stop

@section('css')
<style>
    .form-inline {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 15px;
    }

    .form-inline .form-group {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 0;
    }

    .form-inline .form-group label {
        margin-top: 8px;
        white-space: nowrap;
    }

    .form-inline .form-group .form-control {
        min-width: 200px;
    }

    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220,53,69,.15) !important;
    }

    .table-success  { background-color: #d4edda !important; }
    .table-info     { background-color: #d1ecf1 !important; }
    .table-warning  { background-color: #fff3cd !important; }

    .badge-success {
        background-color: #28a745;
        padding: 8px 12px;
        font-size: 14px;
    }
</style>
@stop

@section('js')
<script>

    /* ─── PAGE LOAD: reset if nothing selected ─── */
    window.addEventListener('load', function () {
        const filterType = document.getElementById('filter_type').value;
        if (!filterType) {
            document.getElementById('filter_id').innerHTML = '<option value="">-- Select --</option>';
            document.getElementById('filter_label').textContent = 'Select Test:';
        }
    });


    /* ─── FRONTEND VALIDATION ─── */
    function validateFilters() {

        let isValid = true;

        let filterType = document.getElementById('filter_type');
        let filterId   = document.getElementById('filter_id');

        let filterTypeError = document.getElementById('filter_type_error');
        let filterIdError   = document.getElementById('filter_id_error');

        // Reset errors
        filterType.classList.remove('is-invalid');
        filterId.classList.remove('is-invalid');
        filterTypeError.style.display = 'none';
        filterIdError.style.display   = 'none';

        // Check Filter Type
        if (filterType.value === '') {
            filterType.classList.add('is-invalid');
            filterTypeError.textContent  = 'Please select a filter type (Test or Course).';
            filterTypeError.style.display = 'block';
            isValid = false;
        }

        // Check Filter ID (Test / Course)
        if (filterId.value === '') {
            filterId.classList.add('is-invalid');

            if (filterType.value === 'test') {
                filterIdError.textContent = 'Please select a Test.';
            } else if (filterType.value === 'course') {
                filterIdError.textContent = 'Please select a Course.';
            } else {
                filterIdError.textContent = 'Please select a value.';
            }

            filterIdError.style.display = 'block';
            isValid = false;
        }

        return isValid;
    }


    /* ─── DYNAMIC DROPDOWN UPDATE ─── */
    function updateFilters() {

        const filterType    = document.getElementById('filter_type').value;
        const filterLabel   = document.getElementById('filter_label');
        const filterIdSelect = document.getElementById('filter_id');

        // Clear errors when user changes selection
        document.getElementById('filter_type').classList.remove('is-invalid');
        document.getElementById('filter_type_error').style.display = 'none';
        document.getElementById('filter_id_error').style.display   = 'none';

        filterIdSelect.innerHTML = '<option value="">-- Select --</option>';

        if (filterType === 'test') {
            filterLabel.textContent = 'Select Test:';
            @if($tests->count() > 0)
                @foreach($tests as $test)
                    filterIdSelect.innerHTML += '<option value="{{ $test->id }}">{{ $test->test_name }}</option>';
                @endforeach
            @endif

        } else if (filterType === 'course') {
            filterLabel.textContent = 'Select Course:';
            @if($packages->count() > 0)
                @foreach($packages as $package)
                    filterIdSelect.innerHTML += '<option value="{{ $package->id }}">{{ $package->name }}</option>';
                @endforeach
            @endif

        } else {
            filterLabel.textContent = 'Select Test:';
        }
    }

</script>
@stop