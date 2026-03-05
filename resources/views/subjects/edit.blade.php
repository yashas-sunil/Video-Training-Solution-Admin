@extends('adminlte::page')

@section('title', 'Edit Subject')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Edit Subject</h3>
        </div>

        <form action="{{ route('subjects.update',$subject->id) }}" method="POST">
            @csrf

            <div class="card-body">

                <!-- Course Dropdown -->
                <div class="form-group">
                    <label>Course</label>

                    <select name="course_id" class="form-control" required>

                        <option value="">Select Course</option>

                        @foreach($courses as $course)

                            <option value="{{ $course->id }}"
                                {{ $subject->course_id == $course->id ? 'selected' : '' }}>

                                {{ $course->title }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <!-- Subject Name -->

                <div class="form-group">
                    <label>Subject Name</label>

                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ $subject->name }}"
                           required>

                </div>

            </div>


            <div class="card-footer">

                <button type="submit" class="btn btn-success">
                    Update
                </button>

                <a href="{{ route('subjects.index') }}" class="btn btn-secondary">
                    Back
                </a>

            </div>

        </form>

    </div>

</div>

@endsection