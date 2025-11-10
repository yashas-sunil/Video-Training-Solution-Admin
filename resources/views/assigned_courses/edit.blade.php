@extends('adminlte::page')

@section('title', 'Edit Assigned Course')

@section('content_header')
    <h1 class="text-left text-dark mb-3">Edit Assigned Course</h1>
@stop

@section('content')
    <div class="d-flex justify-content-left">
        <div class="card shadow-sm col-md-8 " style="border-radius: 10px;">
            <div class="card-body">
                {{-- Success --}}
                @if (session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: '{{ session('success') }}',
                                toast: true,
                                position: 'top-end',
                                timer: 4000,
                                showConfirmButton: false
                            });
                        });
                    </script>
                @endif

                {{-- Error --}}
                @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: '{{ session('error') }}',
                                toast: true,
                                position: 'top-end',
                                timer: 4000,
                                showConfirmButton: false
                            });
                        });
                    </script>
                @endif

                {{-- Validation --}}
                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('assigned-courses.update', $assignedCourse->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- User (readonly) --}}
                    <div class="form-group mb-3">
                        <label for="user_id" class="font-weight-bold">
                            User <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm"
                            value="{{ $assignedCourse->user->name ?? 'N/A' }}" readonly>
                        <input type="hidden" name="user_id" value="{{ $assignedCourse->user_id }}">
                    </div>

                    {{-- Course (readonly) --}}
                    <div class="form-group mb-3">
                        <label for="course_id" class="font-weight-bold">
                            Course <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm"
                            value="{{ $assignedCourse->course->title ?? 'N/A' }}" readonly>
                        <input type="hidden" name="course_id" value="{{ $assignedCourse->course_id }}">
                    </div>

                    {{-- Expire Date & Time (editable, future only) --}}
                    <div class="form-group mb-3">
                        <label for="expire_date" class="font-weight-bold">Expire Date & Time</label>
                        <input type="datetime-local" name="expire_date" id="expire_date"
                            class="form-control form-control-sm"
                            value="{{ $assignedCourse->expire_date ? \Carbon\Carbon::parse($assignedCourse->expire_date)->format('Y-m-d\TH:i') : '' }}"
                            placeholder="Select date and time" onfocus="this.showPicker()" onkeydown="return false"
                            onpaste="return false">
                    </div>

                    <div class="d-flex mt-4" style="gap: 10px">
                        <a href="{{ route('assigned-courses.index') }}" class="btn btn-secondary px-3">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                        <button type="submit" class="btn btn-success px-3">
                            Update <i class="fas fa-save mr-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JS Libraries --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Disable past dates --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const expireInput = document.getElementById('expire_date');
            const now = new Date();
            const localISOTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
                .toISOString().slice(0, 16);
            expireInput.min = localISOTime;
        });
    </script>
@stop
