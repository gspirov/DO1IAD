@extends('layouts.app')

@section('title', $project->title)

@section('content')
    <div class="container py-5"
        {{-- init project comments total count --}}
         x-init="$store.comments.count = {{ $project->comments()->count() }}"
    >
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="mb-4">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                        ← Back to Projects
                    </a>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h1 class="mb-2">{{ $project->title }}</h1>
                            </div>

                            {{-- RIGHT --}}
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-primary fs-6">
                                    {{ $project->phase->value }}
                                </span>

                                @auth
                                    @if(auth()->user()->hasVerifiedEmail())
                                        @can('add-to-favourites', $project)
                                            <form action="{{ route('user-project-favourites.toggle') }}"
                                              method="POST"
                                              x-data="toggleUserFavouriteProjectForm({
                                                  isFav: @js(auth()->user()->favourites->contains($project->id))
                                              })"
                                        >
                                            @csrf

                                            <input type="hidden" name="project_id" value="{{ $project->id }}">

                                            <button type="button"
                                                    class="btn btn-light btn-sm"
                                                    @click="toggle($el.form)"
                                            >
                                                <i class="text-danger bi"
                                                   :class="isFav ? 'bi-heart-fill' : 'bi-heart'"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @endif
                                @endauth
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small mb-1">Start Date</div>
                                    <div class="fw-semibold">
                                        {{ $project->start_date->format('Y-m-d') }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small mb-1">End Date</div>
                                    <div class="fw-semibold">
                                        {{ $project->end_date->format('Y-m-d') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            @include('projects.partials.images', ['images' => $project->images])

                            <h4 class="mb-3">About this project</h4>
                            <p class="mb-0 text-muted" style="white-space: pre-line;">
                                {{ $project->short_description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Project Rating</h5>

                        <div x-data="projectRatingSummary({
                            averageRating: {{ (float) ($project->ratings_avg_rating ?? 0) }},
                            ratingsCount: {{ $project->ratings->count() }},
                        })"
                             @project-rating-updated.window="updateRating($event.detail)"
                             class="mb-3"
                        >
                            <div class="display-6 fw-bold">
                                ★ <span x-text="formattedRating"></span>
                            </div>
                            <div class="text-muted">
                                Based on <span x-text="ratingsCount"></span> ratings
                            </div>
                        </div>

                        @auth
                            @if(auth()->user()->hasVerifiedEmail())
                                @can('rate', $project)
                                    <form action="{{ route('project-ratings.store') }}"
                                      method="POST"
                                      @submit.prevent="handleSubmit"
                                      x-data="projectRatingForm({
                                          rating: @js($currentUserRating)
                                      })"
                                >
                                    @csrf

                                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                                    <div class="mb-3">
                                        <label class="form-label">Your rating</label>
                                        <select name="rating"
                                                class="form-select"
                                                x-model.number="rating"
                                                @change="delete backendErrors.rating"
                                                :class="{ 'is-invalid': hasBackendError('rating') || (shouldShow('rating') && (!hasFilledAttribute('rating') || !isRatingValid())) }"
                                        >
                                            <option value="">Choose rating</option>
                                            <template x-for="star in stars" :key="star.id">
                                                <option x-bind:value="star.id"
                                                        x-bind:selected="rating == star.id"
                                                        x-text="star.label"></option>
                                            </template>
                                        </select>

                                        <div class="invalid-feedback" x-show="hasBackendError('rating')">
                                            <template x-for="(error, index) in backendErrors.rating" :key="index">
                                                <div x-text="error"></div>
                                            </template>
                                        </div>

                                        <div x-show="!hasBackendError('rating') && !isRatingValid()" class="invalid-feedback">
                                            {{ trans('validation.between.numeric', ['attribute' => 'rating', 'min' => 1, 'max' => 5]) }}
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary" :disabled="invalid() || submitted">
                                        Submit Rating
                                    </button>
                                </form>
                                @endcan
                            @else
                                <div class="alert alert-light border mb-0">
                                    Verify your email address to rate this project.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-light border mb-0">
                                <a href="{{ route('login') }}">Log in</a> to rate this project.
                            </div>
                        @endauth
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Project Owner</h5>

                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $project->user->avatar ? asset('storage/' . $project->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($project->user->username) }}"
                                 class="rounded-circle border"
                                 style="width: 80px; height: 80px; object-fit: cover;"
                            >

                            <div>
                                <div class="fw-semibold">{{ $project->user->username }}</div>
                                <div class="text-muted small">{{ $project->user->email }}</div>
                                <div class="text-muted small">Project creator</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Quick Info</h5>

                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <span class="text-muted">Created:</span>
                                <span class="fw-semibold">{{ $project->created_at->format('Y-m-d') }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted">Last updated:</span>
                                <span class="fw-semibold">{{ $project->updated_at->format('Y-m-d') }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted">Comments:</span>
                                <span class="fw-semibold" x-text="$store.comments.count"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                @include('projects.partials.comments', compact('project', 'comments'))
            </div>
        </div>
    </div>
@endsection
