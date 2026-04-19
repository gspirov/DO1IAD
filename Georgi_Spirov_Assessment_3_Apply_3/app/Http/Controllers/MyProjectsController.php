<?php

namespace App\Http\Controllers;

use App\Enums\ProjectPhaseEnum;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/**
 * Controller for managing projects associated with the authenticated user.
 *
 * This controller handles CRUD operations for projects, including listing,
 * creating, editing, updating, and deleting projects. It enforces user-specific
 * access control using authorization gates.
 */
class MyProjectsController extends Controller
{
    /**
     * Display a paginated list of projects belonging to the authenticated user.
     */
    public function index(): View
    {
        $projects = Project::query()
                           ->where('user_id', '=', auth()->user()->id)
                           ->latest('id')
                           ->paginate(10);

        return view('my-projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     * Authorization is checked before rendering the view.
     */
    public function create(): View
    {
        if (!Gate::allows('create', Project::class)) {
            abort(403);
        }

        return view('my-projects.create', [
            'phases' => ProjectPhaseEnum::cases()
        ]);
    }

    /**
     * Store a newly created project in the database.
     *
     * This method creates the project using the validated data from the
     * incoming request and assigns it to the authenticated user.
     * Authorization is checked in ProjectRequest.
     */
    public function store(ProjectRequest $request): RedirectResponse
    {
        Project::create([
            ...$request->validated(),
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->route('my-projects.index')->with('success', 'Project created successfully.');
    }

    /**
     * Show the form for editing the specified project.
     * Authorization is checked before rendering the view.
     */
    public function edit(Project $project): View
    {
        if (!Gate::allows('update', $project)) {
            abort(403);
        }

        return view('my-projects.edit', [
            'phases' => ProjectPhaseEnum::cases(),
            'project' => $project
        ]);
    }

    /**
     * Update the specified project in the database.
     *
     * This method applies the changes provided in the validated request data
     * to the specified project.
     * Authorization is checked in ProjectRequest.
     */
    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());
        return redirect()
               ->route('my-projects.index')
               ->with('success', 'Project updated successfully.');
    }

    /**
     * Delete the specified project from the database.
     *
     * Authorization is checked before attempting to delete the project. A
     * success or error message is returned based on the operation result.
     */
    public function delete(Project $project): RedirectResponse
    {
        if (!Gate::allows('delete', $project)) {
            abort(403);
        }

        $response = redirect()->route('my-projects.index');

        if ($project->delete()) {
            $response->with('success', 'Project deleted successfully.');
        } else {
            $response->with('error', 'Failed to delete project.');
        }

        return $response;
    }
}
