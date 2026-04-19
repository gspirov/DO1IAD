@php

use App\Enums\ProjectPhaseEnum;

@endphp

@extends('layouts.app')

@section('title', 'Portfolio')

@section('content')
    <div class="container py-5">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
            <div>
                <h1 class="mb-1">Discover Projects</h1>
                <p class="text-muted mb-0">
                    Explore ideas, products and experiments from other users
                </p>
            </div>

            @auth
                <div class="mt-3 mt-lg-0">
                    <a href="{{ route('my-projects.create') }}" class="btn btn-primary">
                        Add Project
                    </a>
                </div>
            @endauth
        </div>

        <div class="row g-4">
            <div class="col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="mb-4">Filters</h5>
                        @include('home.partials.filters-form')
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Featured / Results</h5>
                    <span class="text-muted">{{ $projects->total() }} projects found</span>
                </div>

                <div class="d-flex flex-column gap-4">
                    @forelse($projects as $project)
                        @include('home.partials.project-card')
                    @empty
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-5 text-center">
                                <h4 class="mb-2">No projects found</h4>
                                <p class="text-muted mb-0">
                                    Try changing the filters and search again.
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($projects->hasPages())
                    <div class="mt-4">
                        {{ $projects->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
