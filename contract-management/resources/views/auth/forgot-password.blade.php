@extends('layouts.app')

@section('title', 'Forgot Password - Contract Management System')

@section('styles')
<style>
    .reset-container {
        max-width: 500px;
        margin: 80px auto;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        background: white;
        border-top: 4px solid #1C75BC;
    }
    .form-title {
        text-align: center;
        margin-bottom: 25px;
        color: #2c3e50;
        font-weight: 600;
        font-size: 24px;
    }
    .epss-brand {
        text-align: center;
        margin-bottom: 20px;
    }
    .epss-brand img {
        height: 60px;
        margin-bottom: 10px;
    }
    .form-note {
        background: #e9f7fe;
        border-left: 4px solid #1C75BC;
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        font-size: 14px;
    }
    .input-icon {
        position: relative;
    }
    .input-icon i {
        position: absolute;
        left: 15px;
        top: 12px;
        color: #6c757d;
    }
    .input-icon input {
        padding-left: 40px;
    }
    .btn-reset {
        background: #1C75BC;
        border: none;
        padding: 12px;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s;
    }
    .btn-reset:hover {
        background: #1664a0;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(28, 117, 188, 0.2);
    }
    .countdown {
        font-weight: bold;
        color: #1C75BC;
        font-size: 1.2em;
    }
</style>
@endsection

@section('content')
<div style="background: #f5f8fa; min-height: 100vh; padding: 20px 0;">
    <div class="container">
        <div class="reset-container">
            <div class="epss-brand">
                <img src="{{ asset('images/epss_logo.png') }}" alt="EPSS Logo">
                <h4 class="form-title">Password Reset</h4>
            </div>

            @if (session('status'))
                <div class="alert alert-success text-center">
                    <i class="fas fa-check-circle fa-2x mb-3" style="color: #28a745;"></i>
                    <h5>{{ session('status') }}</h5>

                    <p class="mt-4">You will be redirected to the login page in <span id="countdown" class="countdown">5</span> seconds</p>
                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="btn btn-reset btn-lg px-5">Go to Login Now</a>
                    </div>

                    <script>
                        // Countdown timer for redirection
                        let seconds = 5;
                        const countdownElement = document.getElementById('countdown');

                        const countdown = setInterval(function() {
                            seconds--;
                            countdownElement.textContent = seconds;

                            if (seconds <= 0) {
                                clearInterval(countdown);
                                window.location.href = '{{ route('login') }}';
                            }
                        }, 1000);
                    </script>
                </div>
            @else
                <div class="form-note">
                    <i class="fas fa-info-circle mr-2"></i>
                    Enter your email address and we'll send you a temporary password to reset your password.
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="font-weight-bold">Email Address</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control form-control-lg"
                                   id="email" name="email"
                                   placeholder="Enter your email address"
                                   required value="{{ old('email') }}">
                        </div>
                        <small class="form-text text-muted">Example: user@epss.gov.et</small>
                    </div>

                    <button type="submit" class="btn btn-reset btn-lg btn-block mt-4">
                        <i class="fas fa-key mr-2"></i> Send Temporary Password
                    </button>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-primary">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Login
                        </a>
                    </div>

                    <div class="mt-4 alert alert-info">
                        <h6><i class="fas fa-question-circle mr-2"></i> Having trouble?</h6>
                        <ul class="mb-0 pl-3">
                            <li>Ensure you entered the correct email address</li>
                            <li>Check that your email is registered in our system</li>
                            <li>Verify your account is active</li>
                            <li>Contact HR: hr@epss.gov.et or ICT: ict@epss.gov.et</li>
                        </ul>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
