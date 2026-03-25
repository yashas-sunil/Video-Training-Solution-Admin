@extends('adminlte::page')

@section('title', 'Add Subjects')

@section('content')

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-md-10">  {{-- form bigger --}}

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Add Subjects</h3>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger m-3">
                        @foreach ($errors->all() as $error)
                            <p class="mb-0">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('subjects.storesubject') }}" method="POST">
                    @csrf

                    <div class="card-body">

                        <!-- Course Dropdown -->
                        <div class="form-group">
                            <label>Select Course</label>

                            <select name="course_id" class="form-control" required>
                                <option value="">Select Course</option>

                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">
                                        {{ $course->title }}
                                    </option>
                                @endforeach

                            </select>
                        </div>


                        <!-- Subjects -->

                        <div class="form-group">

                            <label>Subjects</label>

                            <div id="subject-wrapper">

                                <div class="input-group mb-2">

                                    <input type="text"
                                           name="subjects[]"
                                           class="form-control"
                                           placeholder="Enter Subject Name">

                                    <div class="input-group-append">

                                        <button type="button"
                                                class="btn btn-success"
                                                onclick="addMore()">
                                                +
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="d-flex" style="gap:10px;">
                            <a href="{{ route('subjects.index') }}" class="btn btn-secondary">
                                Back
                            </a>

                            <button id="submitBtn" type="submit" class="btn btn-success">
                                Save
                            </button>
                        </div>
                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection


@section('js')

<script>

function addMore() {

    let html = `

    <div class="input-group mb-2">

        <input type="text"
               name="subjects[]"
               class="form-control"
               placeholder="Enter Subject Name">

        <div class="input-group-append">

            <button type="button"
                    class="btn btn-danger"
                    onclick="this.closest('.input-group').remove()">
                    -
            </button>

        </div>

    </div>

    `;

    document.getElementById('subject-wrapper')
            .insertAdjacentHTML('beforeend', html);

}

</script>

@endsection