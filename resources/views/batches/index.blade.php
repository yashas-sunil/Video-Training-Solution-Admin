@extends('adminlte::page')

@section('title', 'Batch List')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark">Batch List</h1>

    <a href="{{ route('batches.create') }}" class="btn btn-danger">
        <i class="fas fa-plus"></i> Create Batch
    </a>
</div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body p-0">

        <table class="table table-hover mb-0">
            <thead style="background:#700002; color:white;">
                <tr>
                    <th style="width:70px;">Id</th>
                    <th>Batch Name</th>
                    <th>Course</th>
                    <th>Start Date</th>
                    <th>Expire Date</th>
                    <th>Total Students</th>
                    <th style="width:160px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($batches as $index => $batch)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>
                            <strong>{{ $batch->batch_name }}</strong>
                        </td>

                        <td>
                            {{ $batch->scorm_packages->title ?? '-' }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($batch->start_date)->format('d M Y') }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($batch->expire_date)->format('d M Y') }}
                        </td>

                        <td>
                            <span class="badge badge-primary">
                                {{ $batch->students()->count() }}
                            </span>
                        </td>

                        <td>
                            <a href="{{ route('batches.assign', $batch->id) }}"
                               class="btn btn-sm btn-info">
                                <i class="fas fa-user-plus"></i> Assign
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center p-4">
                            No Batches Found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@stop
