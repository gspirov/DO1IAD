@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="mb-3">Verify Your Email Address</h4>

                    <p class="text-muted">
                        Thanks for signing up! Before getting started, please verify your email address
                        by clicking on the link we just emailed to you.
                    </p>

                    <p class="text-muted">
                        If you didn't receive the email, we will gladly send you another.
                    </p>

                    {{-- Resend button --}}
                    <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            Resend Verification Email
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
