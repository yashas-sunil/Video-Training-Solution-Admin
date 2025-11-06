@extends('adminlte::page')

@section('title', 'Create Admin')

@section('content_header')
    <h1 class="m-0 text-dark">Create User</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <form role="form" id="create" method="POST" action="{{ route('admins.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger"> *</span></label>
                                    <input type="text" id="name" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" placeholder="Name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert" style="display:inline;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Role <span class="text-danger"> *</span></label>
                                    <select name="role_id" id="role_id"
                                            class="form-control select2 @error('role_id') is-invalid @enderror"
                                            style="width:100%;">
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $id => $name)
                                            <option value="{{ $id }}" {{ old('role_id') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <span class="invalid-feedback" role="alert" style="display:inline;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger"> *</span></label>
                                    <input type="email" id="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" placeholder="Email" required
                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                           oninput="this.value=this.value.replace(/\s/g,'')">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert" style="display:inline;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="mobile">Mobile <span class="text-danger"> *</span></label>
                                    <div class="input-group-prepend">
                                        <div class="col-md-3">
                                            <select id="mobile-code"
                                                    class="custom-select @error('mobile_code') is-invalid @enderror"
                                                    name="mobile_code">
                                                <option value="+91" {{ old('mobile_code') == '+91' ? 'selected' : '' }}>+91</option>
                                                <option value="+971" {{ old('mobile_code') == '+971' ? 'selected' : '' }}>+971</option>
                                            </select>
                                            @error('mobile_code')
                                                <span class="invalid-feedback" role="alert" style="display:inline;">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>

                                        <input type="text" id="mobile" name="mobile"
                                               class="form-control @error('mobile') is-invalid @enderror"
                                               value="{{ old('mobile') }}" placeholder="Mobile" maxlength="10"
                                               pattern="[0-9]{10}"
                                               oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                                    </div>
                                    @error('mobile')
                                        <span class="invalid-feedback" role="alert" style="display:inline;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger"> *</span></label>
                                    <input type="password" id="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert" style="display:inline;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex align-items-center" style="gap: 10px">
                        <a href="{{ route('admins.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Create <i class="fas fa-save ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#create').validate({
                rules: {
                    name: { required: true },
                    email: { required: true, maxlength: 255, email: true },
                    role_id: { required: true },
                    password: { required: true, minlength: 6 }
                }
            });

            $("#role_id").select2({
                placeholder: 'Please choose a role'
            });

            @if (session('success'))
                toastr.success("{{ session('success') }}", "Success");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}", "Error");
            @endif
        });
    </script>
@stop
