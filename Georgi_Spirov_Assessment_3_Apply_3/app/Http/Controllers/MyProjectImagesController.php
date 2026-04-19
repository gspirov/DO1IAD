<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadProjectImagesRequest;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;

class MyProjectImagesController extends Controller
{
    /**
     * Display a listing of the images for a specific project.
     */
    public function index(Project $project)
	{
        if (!Gate::allows('update', $project)) {
            abort(403);
        }

        $images = ProjectImage::query()
                              ->where('project_id', '=', $project->id)
                              ->latest('id')
                              ->paginate(10);

        return view('my-projects.images.index', compact('images', 'project'));
    }

    /**
     * Handle the request to upload images to a specific project.
     */
    public function upload(UploadProjectImagesRequest $request, Project $project)
    {
        $validated = $request->validated();

        /* @var UploadedFile $file */
        foreach ($validated['images'] ?? [] as $file) {
            $path = $file->store('projects', 'public');

            ProjectImage::create([
                'path' => $path,
                'project_id' => $project->id
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Images uploaded successfully.'
        ]);
    }

    /**
     * Remove the specified image from a project.
     */
    public function delete(Project $project, ProjectImage $image)
    {
        if (!Gate::allows('delete', $project)) {
            abort(403);
        }

        $image->delete();
        return back()->with('success', 'Successfully deleted image.');
    }
}
