@extends('adminlte::page')

@section('title', 'Subjects List')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Subjects List</h3>

            <div class="card-tools">
                <a href="{{ route('subjects.create') }}" type="button" class="btn btn-success">
                <i class="fas fa-plus"></i> Add Subjects
            </a>
            </div>
        </div>

        <div class="card-body">

            {!! $html->table(['class' => 'table table-bordered table-striped table-hover'], true) !!}

        </div>

    </div>

</div>

@endsection


@section('js')

{!! $html->scripts() !!}

@endsection