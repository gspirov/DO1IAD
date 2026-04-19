@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    Forgot Password
                </div>

                <div class="card-body">
                    <p class="text-muted">
                        Enter your email address and we will send you a password reset link.
                    </p>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   class="form-control @error('email') is-invalid @enderror">

                            @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Send Reset Link
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
