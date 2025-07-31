@extends('adminlte::page')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">📘 Course List</h4>
        <a href="{{ route('courses.create') }}" class="btn btn-primary">
            ➕ Add New Course
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($courses->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Course Title</th>
                        <th>Watch Time (mins)</th>
                        <th>View Limit</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $index => $course)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $course->title }}</td>
                            <td>{{ $course->watch_time ?? '-' }}</td>
                            <td>{{ $course->view_limit ?? '-' }}</td>
                            <td>{{ $course->created_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-warning">✏️ Edit</a>

                                <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">No courses found.</div>
    @endif
</div>
@endsection
