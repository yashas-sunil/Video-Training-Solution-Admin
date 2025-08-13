@extends('adminlte::page')

@section('title', 'Assigned Courses')

@section('content_header')
    <h1>Assigned Courses</h1>
@stop


@section('content')
<div class="container-fluid">
    {{-- Assign Course Button --}}
    <div class="mb-3">
        <a href="{{ route('assigned-courses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Assign New Course
        </a>
    </div>

    {{-- Assigned Courses Table --}}
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Assigned Courses List</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>User</th>
                        <th>Course</th>
                        <th>Enrolled At</th>
                        <th>Expire Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignedCourses as $assign)
                        <tr>
                            <td>{{ $assign->user->name }}</td>
                            <td>{{ $assign->course->title }}</td>
                            <td>{{ \Carbon\Carbon::parse($assign->enrolled_at)->format('d M Y') }}</td>
                            <td>{{ $assign->expire_date ? \Carbon\Carbon::parse($assign->expire_date)->format('d M Y') : 'No Expiry' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
