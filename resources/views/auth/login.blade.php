{{-- @extends('layouts.auth')

@section('content')
<h1 class="mb-4">Login</h1>
@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
<form method="POST" action="{{ route('postLogin') }}" novalidate>
    @csrf
    <div class="mb-3">
        <label for="title" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
        @error('email')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label for="title" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
        @error('password')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" class="btn btn-success">Login</button>
    <a href="{{ route('register') }}">Register</a>
</form>
@endsection --}}

@extends('layouts.auth')

@section('content')
    <div class="register-wrapper d-flex justify-content-center align-items-center">
        <div class="card register-card shadow-lg p-4">

            <h2 class="text-center mb-3 fw-bold text-primary">Welcome Back 👋</h2>
            <p class="text-center text-secondary mb-4">Login to continue</p>

            @if (session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger text-center">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('postLogin') }}" novalidate>
                @csrf
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
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mt-2">Login</button>

                <div class="text-center mt-3">
                    <span class="text-secondary">Don't have an account?</span>
                    <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">Register</a>
                </div>
            </form>

        </div>
    </div>
@endsection