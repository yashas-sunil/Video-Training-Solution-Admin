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

                            <!-- Name -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}"
                                        placeholder="Name">
                                    <div style="min-height:20px;">
                                        @error('name')
                                            <span class="invalid-feedback d-block text-danger" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Role -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Role <span class="text-danger">*</span></label>
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
                                    <div style="min-height:20px;">
                                        @error('role_id')
                                            <span class="invalid-feedback d-block text-danger" role="alert">{{ $message }}</span>
                                        @enderror
                                        <label id="role_id-error" class="error" for="role_id" style="display:none;"></label>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <!-- Email -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <div class="input-wrapper">
                                        <input type="email" id="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}"
                                            placeholder="Email"
                                            oninput="this.value=this.value.replace(/\s/g,'')">
                                        <span class="clear-icon" onclick="clearInput('email')">&times;</span>
                                    </div>
                                    <div style="min-height:20px;">
                                        @error('email')
                                            <span class="invalid-feedback d-block text-danger" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="mobile">Mobile <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-start">
                                        <div class="mr-2" style="width:30%;">
                                            <select id="mobile-code"
                                                class="custom-select @error('mobile_code') is-invalid @enderror"
                                                name="mobile_code">
                                                <option value="+91" {{ old('mobile_code') == '+91' ? 'selected' : '' }}>+91</option>
                                            </select>
                                            <div style="min-height:20px;">
                                                @error('mobile_code')
                                                    <span class="invalid-feedback d-block text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div style="width:70%;">
                                            <input type="text" id="mobile" name="mobile"
                                                class="form-control @error('mobile') is-invalid @enderror"
                                                value="{{ old('mobile') }}"
                                                placeholder="Mobile"
                                                maxlength="10"
                                                oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                                            <div style="min-height:20px;">
                                                @error('mobile')
                                                    <span class="invalid-feedback d-block text-danger" role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <!-- Password -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <!-- input-group wrapped in a div so error goes BELOW the whole group -->
                                    <div>
                                        <div class="input-group">
                                            <input type="password" id="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="Password">
                                            <span class="input-group-text" style="cursor:pointer;" onclick="togglePassword()">
                                                <i id="toggleIcon" class="fa fa-eye"></i>
                                            </span>
                                        </div>
                                        <!-- Error always renders here — below the eye-icon row -->
                                        <div style="min-height:20px;">
                                            @error('password')
                                                <span class="invalid-feedback d-block text-danger" role="alert">{{ $message }}</span>
                                            @enderror
                                            <label id="password-error" class="error" for="password" style="display:none;"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="card-footer d-flex align-items-center" style="gap:10px;">
                        <a href="{{ route('admins.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                        <button type="submit" class="btn btn-success">
                            Save <i class="fas fa-save ml-1"></i>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    /* Clear icon */
    .input-wrapper {
        position: relative;
    }

    .clear-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
        color: #dc3545;
        cursor: pointer;
        display: none;
        z-index: 10;
    }

    .form-control.is-invalid ~ .clear-icon {
        display: block !important;
    }

    .form-control.is-invalid {
        background-image: none !important;
        padding-right: 2.3rem;
    }

    /* Validation */
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
        margin-bottom: 0;
        min-height: 20px;
    }

    /* Password eye-icon border fix when invalid */
    .input-group .form-control.is-invalid {
        border-right: none;
    }

    .input-group .form-control.is-invalid + .input-group-text {
        border-color: #dc3545 !important;
    }
</style>
@stop

@section('js')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function () {

            $('#create').validate({
                rules: {
                    name:        { required: true },
                    email:       { required: true, maxlength: 255, email: true },
                    role_id:     { required: true },
                    password:    { required: true, minlength: 6 },
                    mobile:      { required: true, minlength: 10, maxlength: 10 },
                    mobile_code: { required: true }
                },
                messages: {
                    name:        { required: "This field is required." },
                    email:       { required: "This field is required.", email: "Please enter a valid email." },
                    role_id:     { required: "This field is required." },
                    password:    { required: "This field is required.", minlength: "Password must be at least 6 characters." },
                    mobile:      { required: "This field is required.", minlength: "Mobile must be 10 digits.", maxlength: "Mobile must be 10 digits." },
                    mobile_code: { required: "This field is required." }
                },
                errorElement: 'label',
                errorClass: 'error',
                errorPlacement: function (error, element) {
                    if (element.attr('name') === 'role_id') {
                        // Put inside reserved div
                        $('#role_id-error').replaceWith(error.attr('id', 'role_id-error'));
                    } else if (element.attr('name') === 'password') {
                        // Put inside reserved div below input-group
                        $('#password-error').replaceWith(error.attr('id', 'password-error'));
                    } else if (element.attr('name') === 'mobile_code') {
                        error.insertAfter(element.closest('select'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });

            // Select2
            $("#role_id").select2({ placeholder: 'Please choose a role' });

            // Clear role error on select2 change
            $("#role_id").on('change', function () {
                $('#create').valid();
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
            let icon  = document.getElementById("toggleIcon");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        function clearInput(id) {
            document.getElementById(id).value = '';
        }
    </script>
@stop