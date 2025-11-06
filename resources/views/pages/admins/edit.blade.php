@extends('adminlte::page')

@section('title', 'Edit Admin')

@section('content_header')
    <h1 class="m-0 text-dark">Edit User</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <form role="form" id="edit" method="POST" action="{{ route('admins.update', $admin->id) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            {{-- Name --}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger"> *</span></label>
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $admin->name) }}" placeholder="Name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert" style="display: inline;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Role --}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Role <span class="text-danger"> *</span></label>
                                    <select name="role" id="role"
                                        class="form-control select2 @error('role') is-invalid @enderror"
                                        style="width: 100%;">
                                        <option value="">-- Select Role --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ old('role', $admin->role) == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <span class="invalid-feedback" role="alert" style="display: inline;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Email --}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger"> *</span></label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $admin->email) }}" placeholder="Email" readonly>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert" style="display: inline;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Mobile --}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="mobile">Mobile <span class="text-danger"> *</span></label>
                                    <div class="input-group-prepend">
                                        <div class="col-md-3">
                                            <select id="mobile-code"
                                                class="custom-select @error('mobile_code') is-invalid @enderror"
                                                name="mobile_code">
                                                <option @if ($admin->country_code == '+91') selected @endif value="+91">+91
                                                </option>
                                                <option @if ($admin->country_code == '+971') selected @endif value="+971">
                                                    +971</option>
                                            </select>
                                            @error('mobile_code')
                                                <span class="invalid-feedback" role="alert" style="display: inline;">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>

                                        <input type="text" id="mobile" name="mobile"
                                            class="form-control @error('mobile') is-invalid @enderror"
                                            value="{{ old('mobile', $admin->phone) }}" placeholder="Mobile" maxlength="10"
                                            pattern="[0-9]{10}"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                    </div>
                                    @error('mobile')
                                        <span class="invalid-feedback" role="alert" style="display: inline;">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="card-footer d-flex align-items-center" style="gap: 10px">
                        <div>
                            <a href="{{ route('admins.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                Update <i class="fas fa-save ml-1"></i>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#edit').validate({
                rules: {
                    name: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    role: {
                        required: true
                    },
                    mobile: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    }
                },
                messages: {
                    mobile: {
                        digits: "Please enter only numbers",
                        minlength: "Mobile number must be 10 digits",
                        maxlength: "Mobile number must be 10 digits"
                    }
                }
            });

            $("#role").select2({
                placeholder: 'Please choose a role'
            });
        });
    </script>
@stop
