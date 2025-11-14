@extends('adminlte::page')

@section('title', 'Create Admin')

@section('content_header')
    <h1 class="m-0 text-dark">Create User</h1>
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
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        placeholder="Name">
                                    @error('name')
                                        <span class="invalid-feedback d-block text-danger" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Role <span class="text-danger">*</span></label>
                                    <select name="role_id" id="role_id"
                                        class="form-control select2 @error('role_id') is-invalid @enderror"
                                        style="width:100%;">
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ old('role_id') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <span class="invalid-feedback d-block text-danger" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" placeholder="Email" required
                                        pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                        oninput="this.value=this.value.replace(/\s/g,'')">
                                    @error('email')
                                        <span class="invalid-feedback d-block text-danger" role="alert">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="mobile">Mobile <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-start">
                                        <div class="mr-2" style="width:30%;">
                                            <select id="mobile-code"
                                                class="custom-select @error('mobile_code') is-invalid @enderror"
                                                name="mobile_code">
                                                <option value="+91" {{ old('mobile_code') == '+91' ? 'selected' : '' }}>
                                                    +91</option>
                                                <option value="+971"
                                                    {{ old('mobile_code') == '+971' ? 'selected' : '' }}>+971</option>
                                            </select>
                                            @error('mobile_code')
                                                <span class="invalid-feedback d-block text-danger" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>

                                        <div style="width:70%;">
                                            <input type="text" id="mobile" name="mobile"
                                                class="form-control @error('mobile') is-invalid @enderror"
                                                value="{{ old('mobile') }}" placeholder="Mobile" maxlength="10"
                                                pattern="[0-9]{10}"
                                                oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                                            @error('mobile')
                                                <span class="invalid-feedback d-block text-danger" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>

                                    <div class="input-group">
                                        <input type="password" id="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Password">

                                        <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                                            <i id="toggleIcon" class="fa fa-eye"></i>
                                        </span>
                                    </div>

                                    @error('password')
                                        <span class="invalid-feedback d-block text-danger" role="alert">
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
                        <button type="submit" class="btn btn-success">
                            Create <i class="fas fa-save ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .invalid-feedback {
            color: #dc3545 !important;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        input.is-invalid,
        select.is-invalid {
            border-color: #dc3545 !important;
        }

        label.error {
            color: #dc3545;
            font-size: 0.875rem;
            display: block;
            margin-top: 4px;
        }
    </style>
@stop

@section('js')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
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
                    role_id: {
                        required: true
                    },
                    password: {
                        required: true,
                        minlength: 6
                    },
                    mobile: {
                        required: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    mobile_code: {
                        required: true
                    }
                },
                messages: {
                    name: "This field is required.",
                    email: "This field is required.",
                    role_id: "This field is required.",
                    password: "This field is required.",
                    mobile: "This field is required.",
                    mobile_code: "This field is required."
                },
                errorElement: 'label',
                errorClass: 'error',
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "mobile") {
                        error.insertAfter(element); // place under mobile
                    } else if (element.attr("name") === "mobile_code") {
                        error.insertAfter(element.closest('select'));
                    } else {
                        error.insertAfter(element);
                    }
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

        function togglePassword() {
            let input = document.getElementById("password");
            let icon = document.getElementById("toggleIcon");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
@stop
