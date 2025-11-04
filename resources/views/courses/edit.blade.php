@extends('adminlte::page')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">✏️ Edit Course</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('courses.update', $course->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Course Title <span class="text-danger"> *</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $course->title) }}" class="form-control" required>
                        </div>

                        {{-- <div class="mb-3">
                            <label for="description" class="form-label">Course Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control" required>{{ old('description', $course->description) }}</textarea>
                        </div> --}}

                        <div class="mb-3">
                            <label for="view_limit" class="form-label">View Limit <span class="text-danger"> *</span></label>
                            <input type="number" name="view_limit" id="view_limit" class="form-control" value="{{ old('view_limit', $course->view_limit) }}" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="watch_time" class="form-label">Watch Time (in minutes) <span class="text-danger"> *</span></label>
                            <input type="number" name="watch_time" id="watch_time" class="form-control" value="{{ old('watch_time', $course->watch_time) }}" min="0" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">🔙 Back</a>
                            <button type="submit" class="btn btn-success">💾 Update Course</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
