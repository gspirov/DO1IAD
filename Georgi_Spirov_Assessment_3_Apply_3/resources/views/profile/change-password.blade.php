@extends('layouts.profile')

@section('title', 'Profile')

@section('profile-page-header', 'Change Password')

@section('profile-content')
    <form method="POST"
          action="{{ route('profile.update-password') }}"
          @submit.prevent="handleSubmit"
          x-data="changePasswordForm({
              backendErrors: {
                  current_password: {{ $errors->has('current_password') ? 'true' : 'false' }},
                  password: {{ $errors->has('password') ? 'true' : 'false' }},
                  password_confirmation: {{ $errors->has('password_confirmation') ? 'true' : 'false' }}
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
            <label class="form-label">Current Password</label>
            <input type="password"
                   name="current_password"
                   required
                   x-model="current_password"
                   @input="clearBackendError('current_password')"
                   :class="{ 'is-invalid': hasBackendError('current_password') || (shouldShow('current_password') && !hasFilledAttribute('current_password')) }"
                   class="form-control"
            >

            @error('current_password')
                <div x-show="hasBackendError('current_password')" class="invalid-feedback">{{ $message }}</div>
            @enderror

            <div x-show="!hasBackendError('current_password') && submitted && !hasFilledAttribute('current_password')" class="invalid-feedback">
                {{ trans('validation.required', ['attribute' => 'current password']) }}
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

            <template x-for="rule in passwordRules()" :key="rule.label">
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

        <div class="d-flex">
            <button type="submit" class="btn btn-primary px-4" :disabled="invalid()">
                Save Changes
            </button>
        </div>
    </form>
@endsection
