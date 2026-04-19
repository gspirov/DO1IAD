@extends('layouts.app')

@section('title', 'My Projects')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h5 class="mb-3">Projects</h5>

            <a class="btn btn-primary" href="{{ route('my-projects.create') }}">Create Project</a>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Phase</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->title }}</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $project->phase }}
                                </span>
                            </td>
                            <td>{{ $project->start_date->format('Y-m-d') }}</td>
                            <td>{{ $project->end_date->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('projects.show', $project) }}"
                                   class="btn btn-sm btn-info text-white">
                                    View
                                </a>

                                <a href="{{ route('my-projects.edit', $project) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <a href="{{ route('my-projects.images', $project) }}"
                                   class="btn btn-sm btn-secondary">
                                    Images
                                </a>

                                <form action="{{ route('my-projects.delete', $project) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this project?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No projects uploaded yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($projects->hasPages())
                <div class="mt-4">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
