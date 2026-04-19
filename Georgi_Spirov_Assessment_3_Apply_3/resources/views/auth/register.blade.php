@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    Register
                </div>

                <div class="card-body">
                    <form method="POST"
                          action="{{ route('register') }}"
                          @submit.prevent="handleSubmit"
                          x-data="registerForm({
                              username: @js(old('username')),
                              email: @js(old('email')),
                              backendErrors: {
                                  username: {{ $errors->has('username') ? 'true' : 'false' }},
                                  email: {{ $errors->has('email') ? 'true' : 'false' }},
                                  password: {{ $errors->has('password') ? 'true' : 'false' }},
                                  password_confirmation: {{ $errors->has('password_confirmation') ? 'true' : 'false' }},
                              },
                              passwordRulesTranslations: @js([
                                  'validation.min.string' => trans('validation.min.string', ['attribute' => 'password', 'min' => 8]),
                                  'validation.password.mixed' => trans('validation.password.mixed', ['attribute' => 'password']),
                                  'validation.password.numbers' => trans('validation.password.numbers', ['attribute' => 'password']),
                                  'validation.custom.password.special_char' => trans('validation.custom.password.special_char', ['attribute' => 'password'])
                              ])
                          })"
                    >
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text"
                                   name="username"
                                   required
                                   x-model="username"
                                   class="form-control"
                                   @input="clearBackendError('username')"
                                   :class="{ 'is-invalid': hasBackendError('username') || (shouldShow('username') && (!hasFilledAttribute('username') || !isUsernameLongEnough())) }"
                            >

                            @error('username')
                                <div x-show="hasBackendError('username')" class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div x-show="!hasBackendError('username') && submitted && !hasFilledAttribute('username')" class="invalid-feedback">
                                {{ trans('validation.required', ['attribute' => 'username']) }}
                            </div>

                            <div x-show="!hasBackendError('username') && hasFilledAttribute('username') && !isUsernameLongEnough()" class="invalid-feedback">
                                {{ trans('validation.min.string', ['attribute' => 'username', 'min' => 6]) }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   required
                                   x-model="email"
                                   class="form-control"
                                   @input="clearBackendError('email')"
                                   :class="{ 'is-invalid': hasBackendError('email') || (shouldShow('email') && (!hasFilledAttribute('email') || !isEmailValid())) }"
                            >

                            @error('email')
                                <div x-show="hasBackendError('email')" class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div x-show="!hasBackendError('email') && submitted && !hasFilledAttribute('email')" class="invalid-feedback">
                                {{ trans('validation.required', ['attribute' => 'email']) }}
                            </div>

                            <div x-show="!hasBackendError('email') && hasFilledAttribute('email') && !isEmailValid()" class="invalid-feedback">
                                {{ trans('validation.email', ['attribute' => 'email']) }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password"
                                   name="password"
                                   required
                                   x-model="password"
                                   @input="clearBackendError('password')"
                                   :class="{ 'is-invalid': hasBackendError('password') || (shouldShow('password') && (!hasFilledAttribute('password') || !passwordValid())) }"
                                   class="form-control"
                            >

                            @error('password')
                                <div x-show="hasBackendError('password')" class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div x-show="!hasBackendError('password') && submitted && !hasFilledAttribute('password')" class="invalid-feedback">
                                {{ trans('validation.required', ['attribute' => 'password']) }}
                            </div>

                            <template x-for="rule in passwordRules" :key="rule.label">
                                <div x-show="!hasBackendError('password') && hasFilledAttribute('password') && !rule.valid"
                                     class="invalid-feedback"
                                >
                                    <span x-text="passwordRulesTranslations[rule.label]"></span>
                                </div>
                            </template>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password"
                                   name="password_confirmation"
                                   x-model="password_confirmation"
                                   required
                                   class="form-control"
                                   @input="clearBackendError('password_confirmation')"
                                   :class="{'is-invalid': hasBackendError('password_confirmation') || (shouldShow('password_confirmation') && (!hasFilledAttribute('password_confirmation') || !passwordConfirmValid())) }"
                            >

                            @error('password_confirmation')
                                <div x-show="hasBackendError('password_confirmation')" class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div x-show="!hasBackendError('password_confirmation') && submitted && !hasFilledAttribute('password_confirmation')" class="invalid-feedback">
                                {{ trans('validation.required', ['attribute' => 'password confirmation']) }}
                            </div>

                            <div x-show="!hasBackendError('password_confirmation') && hasFilledAttribute('password_confirmation') && !passwordConfirmValid()" class="invalid-feedback">
                                {{ trans('validation.confirmed', ['attribute' => 'password']) }}
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" :disabled="invalid()">
                            Register
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
