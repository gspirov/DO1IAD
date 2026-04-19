@extends('layouts.profile')

@section('title', 'Profile')

@section('profile-page-header', 'Edit Profile')

@section('profile-content')
    <div class="row g-4 align-items-start">
        <div class="col-md-4 col-lg-3 text-center">
            <form action="{{ route('profile.avatar') }}"
                  method="POST"
                  x-ref="form"
                  enctype="multipart/form-data"
                  x-data="uploadProfilePictureForm({
                      maxFiles: 1,
                      maxFileSizeMb: 5,
                      allowedTypes: ['image/jpeg', 'image/png', 'image/webp'],
                      generalErrorTranslations: @js([
                          'validation.min.array' => trans('validation.min.array', ['attribute' => 'avatar', 'min' => '1']),
                          'validation.max.array' => trans('validation.max.array', ['attribute' => 'avatar', 'max' => '1'])
                      ]),
                      fileErrorTranslations: @js([
                          'validation.mimes' => trans('validation.mimes', ['attribute' => 'avatar', 'values' => 'jpg,jpeg,png,webp']),
                          'validation.max.file' => trans('validation.max.file', ['attribute' => 'avatar', 'max' => 5 * 1024])
                      ]),
                  })"
                  @submit.prevent="handleSubmit()"
            >
                @csrf

                <template x-if="generalErrors.length">
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 ps-3">
                            <template x-for="(error, index) in generalErrors" :key="index">
                                <li x-text="error"></li>
                            </template>
                        </ul>
                    </div>
                </template>

                <template x-if="files.length">
                    <template x-for="(file, index) in files" :key="file.id">
                        <div>
                            {{-- front end validation errors --}}
                            <template x-if="file.errors.length">
                                <div class="text-danger small mt-2">
                                    <template x-for="(error, errorIndex) in file.errors" :key="errorIndex">
                                        <div x-text="`${file.file.name}: ${error}`"></div>
                                    </template>
                                </div>
                            </template>

                            {{-- backend validation errors --}}
                            <template x-if="backendErrors[`images.${index}`]">
                                <div class="text-danger small mt-2">
                                    <template x-for="error in backendErrors[`images.${index}`]">
                                        <div x-text="`${file.file.name}: ${error}`"></div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </template>

                <div class="position-relative d-inline-block">
                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->username) }}"
                         class="rounded-circle border"
                         style="width: 120px; height: 120px; object-fit: cover;"
                    >

                    <label class="position-absolute bottom-0 end-0 bg-dark text-white rounded-circle p-2 shadow"
                           style="cursor: pointer;"
                    >
                        <i class="bi bi-pencil"></i>
                        <input type="file"
                               x-ref="fileInput"
                               name="avatar"
                               class="d-none"
                               @change="uploadProfilePicture($event)"
                               accept="image/jpeg,image/png,image/webp"
                        >
                    </label>
                </div>
            </form>

            <div class="small text-muted mt-2">
                Click to change
            </div>

            <form action="{{ route('profile.delete-avatar') }}"
                  method="POST"
            >
                @csrf

                @if($user->avatar)
                    <button type="submit"
                            name="deleteAvatar"
                            class="btn btn-sm btn-outline-danger"
                    >
                        Clear
                    </button>
                @endif
            </form>
        </div>

        <div class="col-md-8 col-lg-9">
            <form method="POST"
                  action="{{ route('profile.update') }}"
                  @submit.prevent="handleSubmit"
                  x-data="editProfileForm({
                      username: @js(old('username', $user->username)),
                      email: @js(old('email', $user->email)),
                      backendErrors: {
                          username: {{ $errors->has('username') ? 'true' : 'false' }},
                          email: {{ $errors->has('email') ? 'true' : 'false' }}
                      }
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

                <div class="d-flex">
                    <button type="submit" class="btn btn-primary px-4" :disabled="invalid()">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
