@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg rounded-lg">
                <div class="card-header text-center bg-semuaukaryawan text-white rounded-top">
                    <h3>{{ __('Register to ASQI') }}</h3>
                </div>

                <!-- CSS untuk card semua karyawan -->
                <style>
                    .bg-semuaukaryawan {
                        background-color: rgb(255, 94, 0);
                    }
                </style>
                <!-- --- -->

               <div class="card-body">
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Input Name -->
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- ============================================= --}}
        {{-- ---    FORM INPUT NIS DI POSISI YANG BENAR  --- --}}
        {{-- ============================================= --}}
        <div class="mb-3">
            <label for="nis" class="form-label">{{ __('NIS (Nomor Induk)') }}</label>
            <input id="nis" type="text" class="form-control @error('nis') is-invalid @enderror" name="nis" value="{{ old('nis') }}" required autocomplete="nis">
            @error('nis')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        {{-- ============================================= --}}

        <!-- Input Email -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Input Password -->
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Input Confirm Password -->
        <div class="mb-4">
            <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
        </div>

        <!-- Tombol Register -->
        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-gradient-primary px-4 py-2">
                {{ __('Register') }}
            </button>
        </div>
    </form>
</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Custom background *

    /* Button gradient effect */
    .btn-gradient-primary {
        background: linear-gradient(rgb(255, 94, 0));
        color: white;
        border-radius: 30px;
        border: none;
    }

    .btn-gradient-primary:hover {
        background: linear-gradient(rgb(219, 80, 0));
    }

    /* Shadow effect for card */
    .card {
        border-radius: 15px;
    }

    .card-header {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    /* Customize form inputs */
    .form-control {
        border-radius: 30px;
    }

    .invalid-feedback {
        font-size: 0.9rem;
        color: #dc3545;
    }

    /* Input hover effect */
    .form-control:focus {
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
    }

    /* Custom label styling */
    .form-label {
        font-weight: 600;
    }

    /* CSS untuk card semua karyawan */
    .bg-semuaukaryawan {
        background-color: rgb(255, 94, 0);
    }
</style>