@extends('adminlte::page')

@section('title', 'Course Chapters')

@section('content_header')
    <h1>Course Chapters</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @if($chapters->count() == 0)
            <p>No chapters found.</p>
        @endif

        <ul class="list-group">
            @foreach($chapters as $chapter)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $chapter->name }}

                    <a href="{{ route('chapter.view', $chapter->id) }}"
                       class="btn btn-sm btn-primary">
                        Open
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@stop
