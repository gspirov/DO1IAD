@extends('layouts.app')

@section('title', 'Update Project')

@section('content')
    <div class="mb-3">
        <a href="{{ route('my-projects.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>

    <form action="{{ route('my-projects.update', $project) }}"
          method="POST"
          @submit.prevent="handleSubmit"
          x-data="projectForm({
              title: @js($project->title),
              short_description: @js($project->short_description),
              start_date: @js($project->start_date->format('Y-m-d')),
              end_date: @js($project->end_date->format('Y-m-d')),
              phase: @js($project->phase->value),
              backendErrors: {
                  title: {{ $errors->has('title') ? 'true' : 'false' }},
                  short_description: {{ $errors->has('short_description') ? 'true' : 'false' }},
                  start_date: {{ $errors->has('start_date') ? 'true' : 'false' }},
                  end_date: {{ $errors->has('end_date') ? 'true' : 'false' }},
                  phase: {{ $errors->has('phase') ? 'true' : 'false' }}
              }
          })"
    >
        @include('my-projects._form')
    </form>
@endsection
