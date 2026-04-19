@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">Login</div>

                <div class="card-body">
                    <form method="POST"
                          action="{{ route('login') }}"
                          @submit.prevent="handleSubmit"
                          x-data="loginForm()"
                    >
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text"
                                   name="email"
                                   required
                                   x-model="email"
                                   class="form-control"
                                   :class="{ 'is-invalid': shouldShow('email') && (!hasFilledAttribute('email') || !isEmailValid()) }"
                            >

                            <div x-show="submitted && !hasFilledAttribute('email')" class="invalid-feedback">
                                {{ trans('validation.required', ['attribute' => 'email']) }}
                            </div>

                            <div x-show="hasFilledAttribute('email') && !isEmailValid()" class="invalid-feedback">
                                {{ trans('validation.email', ['attribute' => 'email']) }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password"
                                   name="password"
                                   required
                                   x-model="password"
                                   :class="{ 'is-invalid': shouldShow('password') && !hasFilledAttribute('password') }"
                                   class="form-control"
                            >

                            <div x-show="submitted && !hasFilledAttribute('password')" class="invalid-feedback">
                                {{ trans('validation.required', ['attribute' => 'password']) }}
                            </div>

                            <div class="text-end mt-2">
                                <a href="{{ route('password.request') }}" class="small text-decoration-none">
                                    Forgot password?
                                </a>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Log in
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
