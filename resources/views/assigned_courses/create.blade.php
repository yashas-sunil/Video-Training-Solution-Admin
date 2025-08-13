@extends('adminlte::page')

@section('title', 'Assign Course')

@section('content_header')
    <h1>Assign Course to User</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Assign Course</h3>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success m-3">
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="alert alert-danger m-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('assigned-courses.store') }}" method="POST">
            @csrf
            <div class="card-body">

                {{-- Select User --}}
                <div class="form-group">
                    <label for="user_id">Select User <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">-- Select User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Select Course --}}
                <div class="form-group">
                    <label for="course_id">Select Course <span class="text-danger">*</span></label>
                    <select name="course_id" id="course_id" class="form-control" required>
                        <option value="">-- Select Course --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Expire Date --}}
                <div class="form-group">
                    <label for="expire_date">Expire Date</label>
                    <input type="date" name="expire_date" id="expire_date" class="form-control">
                </div>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Assign Course</button>
            </div>
        </form>
    </div>
</div>
@stop
