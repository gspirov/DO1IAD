<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <a href="{{ route('projects.show', $project) }}" class="stretched-link"></a>

        <div class="row g-3 align-items-start">
            <div class="col-md-3 col-lg-2">
                <div class="bg-light rounded overflow-hidden position-relative"
                     style="height: 120px;">

                    @if($project->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $project->images->first()->path) }}"
                             alt="{{ $project->title }}"
                             class="w-100 h-100 object-fit-cover">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted small">
                            No image
                        </div>
                    @endif

                    @if($project->images->count() > 1)
                        <span class="position-absolute top-0 end-0 m-1 badge bg-dark bg-opacity-75">
                            +{{ $project->images->count() - 1 }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-md-9 col-lg-10">
                <div class="d-flex flex-column h-100">

                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h4 class="mb-1">{{ $project->title }}</h4>
                            <div class="text-muted small">
                                by {{ $project->user->username }}
                            </div>
                        </div>

                        <span class="badge bg-primary">
                            {{ $project->phase->value }}
                        </span>
                    </div>

                    <div class="mb-2 text-muted small">
                        📅 {{ $project->start_date?->format('Y-m-d') }} – {{ $project->end_date?->format('Y-m-d') }}
                    </div>

                    <p class="mb-3">
                        {{ $project->short_description }}
                    </p>

                    <div class="mt-auto d-flex align-items-center gap-3 small text-muted">
                        <span class="d-flex align-items-center gap-1 fw-semibold text-dark">
                            <span class="text-warning">★</span>
                            {{ number_format((float) ($project->ratings_avg_rating ?? 0), 1) }}
                        </span>

                        <span class="d-flex align-items-center gap-1">
                            <i class="bi bi-chat"></i>
                            {{ $project->comments_count ?? 0 }}
                        </span>

                        <span class="d-flex align-items-center gap-1">
                            <i class="bi bi-heart-fill text-danger"></i>
                            {{ $project->favourite_by_users_count }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
