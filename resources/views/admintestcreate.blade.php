@extends('adminlte::page')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
@endpush

@push('js')
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
@endpush

@section('title', 'Admin Test creation')

@section('content_header')
    <div class="row">
        <div class="col">
            <h1 class="m-0 text-dark">Create Championship Test</h1>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="adminTestForm" action="{{ route('admin-test.store') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        
                        {{-- Test Name --}}
                        <div class="form-group row">
                            <label for="test_name" class="col-md-2 col-form-label">Test Name <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="text" class="form-control @error('test_name') is-invalid @enderror" 
                                    id="test_name" name="test_name" placeholder="Enter test name" 
                                    value="{{ old('test_name') }}" required>
                                @error('test_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Course Selection --}}
                        <div class="form-group row">
                            <label for="course_id" class="col-md-2 col-form-label">Course <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select class="form-control @error('course_id') is-invalid @enderror" 
                                    id="course_id" name="course_id" required>
                                    <option value="">-- Select Course --</option>
                                    @foreach($course as $courses)
                                        <option value="{{ $courses->id }}" {{ old('course_id') == $courses->id ? 'selected' : '' }}>
                                            {{ $courses->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Subject Selection --}}
                        <div class="form-group row">
                            <label for="subject_ids" class="col-md-2 col-form-label">Subject <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select class="form-control @error('subject_ids') is-invalid @enderror" 
                                    id="subject_ids" name="subject_ids[]" multiple="multiple" required>
                                    <option value="">-- Select Subjects --</option>
                                </select>
                                @error('subject_ids')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Total Questions Count --}}
                        <div class="form-group row">
                            <label for="total_ques_count" class="col-md-2 col-form-label">Total Questions <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="number" class="form-control @error('total_ques_count') is-invalid @enderror" 
                                    id="total_ques_count" name="total_ques_count" placeholder="Enter total number of questions" 
                                    value="{{ old('total_ques_count') }}" min="1" required>
                                @error('total_ques_count')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Easy Count --}}
                        <div class="form-group row">
                            <label for="easy_count" class="col-md-2 col-form-label">Easy Questions <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="number" class="form-control @error('easy_count') is-invalid @enderror" 
                                    id="easy_count" name="easy_count" placeholder="Enter number of easy questions" 
                                    value="{{ old('easy_count') }}" min="0" required>
                                @error('easy_count')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Medium Count --}}
                        <div class="form-group row">
                            <label for="medium_count" class="col-md-2 col-form-label">Medium Questions <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="number" class="form-control @error('medium_count') is-invalid @enderror" 
                                    id="medium_count" name="medium_count" placeholder="Enter number of medium questions" 
                                    value="{{ old('medium_count') }}" min="0" required>
                                @error('medium_count')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Hard Count --}}
                        <div class="form-group row">
                            <label for="hard_count" class="col-md-2 col-form-label">Hard Questions <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="number" class="form-control @error('hard_count') is-invalid @enderror" 
                                    id="hard_count" name="hard_count" placeholder="Enter number of hard questions" 
                                    value="{{ old('hard_count') }}" min="0" required>
                                @error('hard_count')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Status --}}
                        {{-- <div class="form-group row">
                            <label for="status" class="col-md-2 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                    <option value="">-- Select Status --</option>
                                    <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}

                        {{-- Buttons --}}
                        <div class="form-group row">
                            <div class="col-md-10 offset-md-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Save
                                </button>
                                <a href="{{ route('admin-test') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function () {
            $('#course_id').on('change', function () {
                let courseId = $(this).val();
                $('#subject_ids').empty().append('<option value="">-- Select Subjects --</option>');
                
                if (courseId) {
                    $.ajax({
                        url: '{{ url('/api/subjects') }}' + '/' + courseId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            if (data.subjects && data.subjects.length > 0) {
                                $.each(data.subjects, function (key, subject) {
                                    $('#subject_ids').append('<option value="' + subject.id + '">' + subject.name + '</option>');
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop