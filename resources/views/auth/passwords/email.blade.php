@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg rounded-lg mt-5">
                <div class="card-header text-center bg-reset text-white rounded-top">
                    <h3>{{ __('Reset Password') }}</h3>
                </div>

                <style>
                    .bg-reset {
                        background-color: rgb(193, 39, 45);
                    }
                </style>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <div class="input-group">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                    placeholder="Enter your registered email">
                            </div>

                            @error('email')
                            <div class="invalid-feedback d-block">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-gradient-primary px-4 py-2">
                                <i class="fas fa-paper-plane me-2"></i>
                                {{ __('Send Password Reset Link') }}
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>
                                {{ __('Back to Login') }}
                            </a>
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
    /* Custom background */
    body {
        background: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    /* Button gradient effect */
    .btn-gradient-primary {
        background: linear-gradient(45deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        border-radius: 30px;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-gradient-primary:hover {
        background: linear-gradient(45deg, #2575fc 0%, #6a11cb 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    /* Shadow effect for card */
    .card {
        border-radius: 15px;
        border: none;
    }

    .card-header {
        border-top-left-radius: 15px !important;
        border-top-right-radius: 15px !important;
    }

    /* Customize form inputs */
    .form-control {
        border-radius: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 12px 20px;
    }

    .form-control:focus {
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
    }

    .input-group-text {
        border-radius: 30px 0 0 30px !important;
        background: linear-gradient(45deg, #6a11cb 0%, #2575fc 100%);
        border: none;
    }

    /* Custom label styling */
    .form-label {
        font-weight: 600;
        color: #495057;
    }

    /* Alert styling */
    .alert {
        border-radius: 10px;
    }

    /* Link styling */
    a {
        color: #6a11cb;
        transition: all 0.3s ease;
    }

    a:hover {
        color: #2575fc;
        text-decoration: underline;
    }
</style>
@endsection

@section('scripts')
@if(session('status'))
<script>
    // Auto-dismiss alert after 5 seconds
    setTimeout(function() {
        $('.alert').fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 5000);
</script>
@endif
@endsection