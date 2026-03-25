@extends('adminlte::page')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Course</h1>
@stop

@section('content')
<div class="container py-2">
    <div class="row justify-content-left">
        <div class="col-md-8">

            <div class="card shadow-sm border-0">
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

                        {{-- VIEW LIMIT --}}
                        <div class="mb-3">
                            <label for="view_limit" class="form-label">View Limit <span class="text-danger"> *</span></label>
                            <input type="number" 
                                   name="view_limit" 
                                   id="view_limit" 
                                   class="form-control" 
                                   value="{{ old('view_limit', $course->view_limit) }}" 
                                   min="1"
                                   max="200"
                                   required
                                   oninput="limitView(this)">
                            <span class="text-danger" id="viewError"></span>
                        </div>

                        {{-- WATCH TIME --}}
                        <div class="mb-3">
                            <label for="watch_time" class="form-label">
                                Watch Time (in minutes) <span class="text-danger">*</span>
                            </label>

                            <input type="number" 
                                   name="watch_time" 
                                   id="watch_time" 
                                   class="form-control" 
                                   value="{{ old('watch_time', $course->watch_time) }}"
                                   min="1"
                                   max="3000"
                                   required
                                   oninput="limitInput(this)">

                            <span class="text-danger" id="watchError"></span>
                        </div>

                        <div class="d-flex" style="gap: 10px">
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i>Back
                            </a>
                            <button type="submit" class="btn btn-success">
                                Update Course<i class="fas fa-save ml-1"></i>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
// ✅ WATCH TIME LIMIT (3000)
function limitInput(el) {
    let max = 3000;

    if (el.value.length > 4) {
        el.value = el.value.slice(0, 4);
    }

    if (parseInt(el.value) > max) {
        document.getElementById("watchError").innerText = "Max allowed is 3000 minutes (50 hours)";
        el.value = max;
    } else {
        document.getElementById("watchError").innerText = "";
    }
}

// ✅ VIEW LIMIT (200)
function limitView(el) {
    let max = 200;

    if (el.value.length > 3) {
        el.value = el.value.slice(0, 3);
    }

    if (parseInt(el.value) > max) {
        document.getElementById("viewError").innerText = "Max 200 views allowed.";
        el.value = max;
    } else {
        document.getElementById("viewError").innerText = "";
    }
}
</script>

@endsection