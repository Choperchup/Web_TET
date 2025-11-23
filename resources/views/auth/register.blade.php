@extends('layouts.auth')

@section('content')
    <div class="register-wrapper d-flex justify-content-center align-items-center">
        <div class="card register-card shadow-lg p-4">

            <h2 class="text-center mb-3 fw-bold text-primary">Create Account ✨</h2>
            <p class="text-center text-secondary mb-4">Join us and start your journey</p>

            @if (session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('postRegister') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Name</label>
                    <input type="text" class="form-control form-control-lg" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control form-control-lg" name="email" value="{{ old('email') }}"
                        required>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" class="form-control form-control-lg" name="password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" class="form-control form-control-lg" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Register</button>

                <div class="text-center mt-3">
                    <span class="text-secondary">Already have an account?</span>
                    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Login</a>
                </div>
            </form>

        </div>
    </div>
@endsection