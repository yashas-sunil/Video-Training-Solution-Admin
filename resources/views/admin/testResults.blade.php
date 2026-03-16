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
                <form method="GET" action="{{ url('test-result') }}" class="form-inline">
                    <div class="form-group mr-3">
                        <label for="filter_type" class="mr-2">Filter By:</label>
                        <select id="filter_type" name="filter_type" class="form-control" onchange="updateFilters()">
                            <option value="">-- Select --</option>
                            <option value="test" {{ $filterType === 'test' ? 'selected' : '' }}>Test</option>
                            <option value="course" {{ $filterType === 'course' ? 'selected' : '' }}>Course</option>
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        <label for="filter_id" class="mr-2" id="filter_label">Select Test:</label>
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
                    </div>

                    <button type="submit" class="btn btn-primary">View Leaderboard</button>
                    <a href="{{ url('test-result') }}" class="btn btn-secondary ml-2">Clear Filters</a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Leaderboard Section -->
@if(!empty($leaderboard) && count($leaderboard) > 0)
    <div class="card shadow-sm">
        <div class="card-header" style="background:primary; color:rgb(0, 0, 0);">
            {{-- <h5 class="m-0">
                @if($filterType === 'test')
                    Leaderboard - Test: {{ $leaderboard[0]->test_name ?? 'N/A' }}
                @else
                    Leaderboard - Course: {{ $leaderboard[0]->course_name ?? 'N/A' }}
                @endif
            </h5> --}}
             <h5 class="m-0">
                    Leaderboard
            </h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead style="background:#f5f5f5;">
                    <tr>
                        <th width="5%" style="text-align:center;"><strong>Rank</strong></th>
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
                                @if($index === 0)
                                     {{ $index + 1 }}
                                @elseif($index === 1)
                                     {{ $index + 1 }}
                                @elseif($index === 2)
                                     {{ $index + 1 }}
                                @else
                                    {{ $index + 1 }}
                                @endif
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
        <strong>No results found.</strong> Please select a test or course to view the leaderboard.
    </div>
@endif

@stop

@section('css')
<style>
    .form-inline .form-group {
        display: inline-block;
    }
    
    .table-success {
        background-color: #d4edda !important;
    }
    
    .table-info {
        background-color: #d1ecf1 !important;
    }
    
    .table-warning {
        background-color: #fff3cd !important;
    }
    
    .badge-success {
        background-color: #28a745;
        padding: 8px 12px;
        font-size: 14px;
    }
</style>
@stop

@section('js')
<script>
    // Initialize filters on page load
    window.addEventListener('load', function() {
        const filterType = document.getElementById('filter_type').value;
        const filterIdSelect = document.getElementById('filter_id');
        const filterLabel = document.getElementById('filter_label');
        
        // If no filter type selected, clear everything
        if (!filterType) {
            filterIdSelect.innerHTML = '<option value="">-- Select --</option>';
            filterLabel.textContent = 'Select Test:';
        }
    });

    function updateFilters() {
        const filterType = document.getElementById('filter_type').value;
        const filterLabel = document.getElementById('filter_label');
        const filterIdSelect = document.getElementById('filter_id');
        
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
        }
    }
</script>
@stop