@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg rounded-lg mt-5">
                <div class="card-header text-center bg-semuaukaryawan text-white rounded-top">
                    <h3>{{ __('Login to ASQI') }}</h3>
                </div>

                <!-- CSS untuk card semua karyawan -->
                <style>
                    .bg-semuaukaryawan {
                        background-color: rgb(193, 39, 45);
                    }
                </style>
                <!-- --- -->

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>

                        <div class="mb-4 d-flex justify-content-between">
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                    
                            <div>
                                @if (Route::has('password.request'))
                                <a class="btn btn-link text-muted" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-gradient-primary px-4 py-2">
                                {{ __('Login') }}
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
<!-- <style>
    /* Custom background */
    body {
        background: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
    }

    /* Button gradient effect */
    .btn-gradient-primary {
        background: linear-gradient(45deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        border-radius: 30px;
        border: none;
    }

    .btn-gradient-primary:hover {
        background: linear-gradient(45deg, #2575fc 0%, #6a11cb 100%);
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
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
</style> -->
@endsection