@extends('adminlte::page')

@section('title', 'Create Admin')

@section('content_header')
    <h1 class="m-0 text-dark">Create Admin</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <form role="form" id="create" method="POST" action="{{ route('admins.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        placeholder="Name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert" style="display: inline;">
                                            {{ $errors->first('name') }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="role" id="role"
                                        class="form-control select2 @error('role') is-invalid @enderror"
                                        style="width: 100%;">
                                        <option></option>
                                        <option value="{{ App\Models\User::ROLE_ADMIN }}"
                                            @if (old('role') == App\Models\User::ROLE_ADMIN) selected @endif>
                                            {{ App\Models\User::ROLE_ADMIN_TEXT }}
                                        </option>
                                        <option value="2" @if (old('role') == 2) selected @endif>
                                            User Course Add
                                        </option>
                                    </select>
                                    @error('role')
                                        <span class="invalid-feedback" role="alert" style="display: inline;">
                                            {{ $errors->first('role') }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" placeholder="Email" required
                                        pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                        oninput="this.value = this.value.replace(/\s/g, '')">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert" style="display: inline;">
                                            {{ $errors->first('email') }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="mobile">Mobile</label>
                                    <div class="input-group-prepend">
                                        <div class="col-md-3">
                                            <select id="mobile-code"
                                                class="custom-select @error('mobile_code') is-invalid @enderror"
                                                name="mobile_code">
                                                <option @if (old('mobile_code') == '+91') selected @endif value="+91">
                                                    +91
                                                </option>
                                                <option @if (old('mobile_code') == '+971') selected @endif value="+971">
                                                    +971
                                                </option>
                                            </select>
                                            @error('mobile_code')
                                                <span class="invalid-feedback" role="alert" style="display: inline;">
                                                    {{ $errors->first('mobile_code') }}
                                                </span>
                                            @enderror
                                        </div>

                                        <input type="text" id="mobile" name="mobile"
                                            class="form-control @error('mobile') is-invalid @enderror"
                                            value="{{ old('mobile') }}" placeholder="Mobile" maxlength="10"
                                            pattern="[0-9]{10}"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                    </div>

                                    @error('mobile')
                                        <span class="invalid-feedback" role="alert" style="display: inline;">
                                            {{ $errors->first('mobile') }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert" style="display: inline;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('admins.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                        <div class="d-flex justify-content-end flex-grow-1">
                            <button type="submit" class="btn btn-primary">
                                Create <i class="fas fa-save ml-1"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    {{--  Toastr CSS + JS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // jQuery Validation
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
                placeholder: 'Please choose a role'
            });

            // Toastr popup for success or error
            @if (session('success'))
                toastr.success("{{ session('success') }}", "Success", {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: 4000
                });
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}", "Error", {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: 4000
                });
            @endif
        });
    </script>
@stop
