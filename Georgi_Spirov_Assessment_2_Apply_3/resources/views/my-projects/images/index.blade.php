@extends('layouts.app')

@section('title', 'Project Images')

@section('content')
    <div class="mb-3">
        <a href="{{ route('my-projects.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h5 class="mb-3">Project Images</h5>

            <form x-data="uploadProjectImageForm({
                      maxFiles: 5,
                      maxFileSizeMb: 5,
                      allowedTypes: ['image/jpeg', 'image/png', 'image/webp'],
                      generalErrorTranslations: @js([
                          'validation.min.array' => trans('validation.min.array', ['attribute' => 'files', 'min' => '1']),
                          'validation.max.array' => trans('validation.max.array', ['attribute' => 'files', 'max' => '5'])
                      ]),
                      fileErrorTranslations: @js([
                          'validation.mimes' => trans('validation.mimes', ['attribute' => 'file', 'values' => 'jpg,jpeg,png,webp']),
                          'validation.max.file' => trans('validation.max.file', ['attribute' => 'file', 'max' => 5 * 1024])
                      ]),
                  })"
                  @submit.prevent="handleSubmit()"
                  action="{{ route('my-projects.images.upload', $project) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="card shadow-sm border-0"
            >
                @csrf

                <div class="mb-3">
                    <input id="images"
                           x-ref="fileInput"
                           type="file"
                           name="images[]"
                           multiple
                           class="form-control"
                           :class="{ 'is-invalid': invalid() }"
                           @change="handleFiles($event)"
                           accept="image/jpeg,image/png,image/webp"
                    >

                    <div class="form-text">
                        Max 5 files, up to 5 MB each. Allowed: JPG, PNG, WEBP.
                    </div>
                </div>

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

                <button type="submit"
                        class="btn btn-primary"
                        :disabled="submitted || !files.length || invalid()"
                >
                    <span x-show="!submitted">Upload</span>
                    <span x-show="submitted">Uploading...</span>
                </button>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($images as $image)
                        <tr>
                            <td>{{ $image->id }}</td>
                            <td>
                                <img src="{{ asset('storage/' . $image->path) }}"
                                     alt="image"
                                     style="width: 60px; height: 60px; object-fit: cover;"
                                     class="rounded border"
                                >
                            </td>
                            <td>{{ $image->created_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $image->updated_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                <form action="{{ route('my-projects.images.delete', [$project, $image]) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this image?')"
                                >
                                    @csrf

                                    <button class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No images uploaded yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($images->hasPages())
                <div class="mt-4">
                    {{ $images->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
