@extends('adminlte::page')

@section('title', 'Create Admin')

@section('content_header')
    <h1 class="m-0 text-dark">Create Admin</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <form role="form" id="create" method="POST" action="{{ route('admins.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Name">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert" style="display: inline;">{{ $errors->first('name') }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="role" id="role" class="form-control select2 select2-hidden-accessible @error('role') is-invalid @enderror" style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true">
                                        <option></option>
                                        <option data-select2-id="{{ App\Models\User::ROLE_ADMIN }}" @if( old('role')==App\Models\User::ROLE_ADMIN) selected @endif  value="{{ App\Models\User::ROLE_ADMIN }}">{{ App\Models\User::ROLE_ADMIN_TEXT }}</option>
                                        {{-- <option data-select2-id="{{ App\Models\User::ROLE_REPORT_ADMIN }}" @if( old('role')==App\Models\User::ROLE_REPORT_ADMIN) selected @endif  value="{{ App\Models\User::ROLE_REPORT_ADMIN }}">{{ App\Models\User::ROLE_REPORT_ADMIN_TEXT }}</option>
                                        <option data-select2-id="{{ App\Models\User::ROLE_CONTENT_MANAGER }}" @if( old('role')==App\Models\User::ROLE_CONTENT_MANAGER) selected @endif  value="{{ App\Models\User::ROLE_CONTENT_MANAGER }}">{{ App\Models\User::ROLE_CONTENT_MANAGER_TEXT }}</option>
                                        <option data-select2-id="{{ App\Models\User::ROLE_FINANCE_MANAGER }}" @if( old('role')==App\Models\User::ROLE_FINANCE_MANAGER) selected @endif  value="{{ App\Models\User::ROLE_FINANCE_MANAGER }}">{{ App\Models\User::ROLE_FINANCE_MANAGER_TEXT }}</option>
                                        <option data-select2-id="{{ App\Models\User::ROLE_BRANCH_MANAGER }}" @if( old('role')==App\Models\User::ROLE_BRANCH_MANAGER) selected @endif  value="{{ App\Models\User::ROLE_BRANCH_MANAGER }}">{{ App\Models\User::ROLE_BRANCH_MANAGER_TEXT }}</option>
                                        <option data-select2-id="{{ App\Models\User::ROLE_ASSISTANT }}" @if( old('role')==App\Models\User::ROLE_ASSISTANT) selected @endif  value="{{ App\Models\User::ROLE_ASSISTANT }}">{{ App\Models\User::ROLE_ASSISTANT_TEXT }}</option>
                                        <option data-select2-id="{{ App\Models\User::ROLE_REPORTING }}" @if( old('role')==App\Models\User::ROLE_REPORTING) selected @endif  value="{{ App\Models\User::ROLE_REPORTING }}">{{ App\Models\User::ROLE_REPORTING_TEXT }}</option>
                                        <option data-select2-id="{{ App\Models\User::ROLE_BACKOFFICE_MANAGER }}" @if( old('role')==App\Models\User::ROLE_BACKOFFICE_MANAGER) selected @endif  value="{{ App\Models\User::ROLE_BACKOFFICE_MANAGER }}">{{ App\Models\User::ROLE_BACKOFFICE_MANAGER_TEXT }}</option>
                                        <option data-select2-id="{{ App\Models\User::ROLE_JUNIOR_ADMIN }}" @if( old('role')==App\Models\User::ROLE_JUNIOR_ADMIN) selected @endif  value="{{ App\Models\User::ROLE_JUNIOR_ADMIN }}">{{ App\Models\User::ROLE_JUNIOR_ADMIN_TEXT }}</option> --}}
                                        <option data-select2-id="2" @if( old('role')==2) selected @endif value="2">User Course Add</option>
                                    </select>
                                    @error('role')
                                    <span class="invalid-feedback" role="alert" style="display: inline;">{{ $errors->first('role') }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="text" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Email">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert" style="display: inline;">{{ $errors->first('email') }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="mobile">Mobile</label>
                                    <div class="input-group-prepend">
                                        <div class="col-md-3">
                                            <select id="mobile-code" class="custom-select @error('mobile_code') is-invalid @enderror" name="mobile_code">
                                                <option @if( old('mobile_code')=="+91") selected @endif value="+91">+91</option>
                                                <option @if( old('mobile_code')=="+971") selected @endif value="+971">+971</option>
                                            </select>
                                            @error('mobile_code')
                                            <span class="invalid-feedback" role="alert" style="display: inline;">{{ $errors->first('mobile_code') }}</span>
                                            @enderror
                                        </div>
                                        <input type="text" id="mobile" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" placeholder="Mobile">
                                    </div>
                                    @error('mobile')
                                    <span class="invalid-feedback" role="alert" style="display: inline;">{{ $errors->first('mobile') }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                                    @error('password')
                                    <span class="invalid-feedback" role="alert" style="display: inline;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            $('#create').validate({
                rules: {
                    name: {
                        required: true
                    },
                    email: {
                        required: true,
                        maxlength: 255,
                        email: true
                    },
                    role: {
                        required: true
                    },
                    password: {
                        required: true,
                        minlength: 6
                    }
                }
            });

            $("#role").select2({
                placeholder: 'Please chose a role'
            });
        });
    </script>
@stop
