@extends('adminlte::page')

@section('title', 'Chapter List')
<style>
.dt-buttons {
    display: none !important;
}
</style>

@section('content_header')
     <div class="col text-right">
            <a href="{{ route('chapter.scorm.create') }}" type="button" class="btn btn-success">
                <i class="fas fa-plus"></i> Add Course
            </a>
        </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        {!! $html->table(['class' => 'table table-bordered table-striped'], true) !!}
    </div>
</div>
@stop

@section('js')
{!! $html->scripts() !!}
@stop
