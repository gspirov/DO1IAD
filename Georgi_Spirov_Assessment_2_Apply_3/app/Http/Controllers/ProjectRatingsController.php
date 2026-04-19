<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRatingRequest;
use App\Models\Project;
use App\Models\ProjectRating;

class ProjectRatingsController extends Controller
{
    /**
     * Store a project rating submitted by a user.
     */
    public function store(StoreProjectRatingRequest $request)
    {
        $validated = $request->validated();

        /**
         * Get project from the request and if exist then check if the user is authorized to rate on it.
         */
        $project = Project::find($validated['project_id']);

        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $existingRating = $project->ratings()->where('user_id', $request->user()->id);

        /**
         * If the user already rated the project then update the rating, otherwise create a new rating.
         */
        if ($existingRating->exists()) {
            /**
             * If the rating is null then delete the rating, otherwise update the rating.
             */
            if ($validated['rating'] === null) {
                $existingRating->delete();
            } else {
                $existingRating->update(['rating' => $validated['rating']]);
            }
        } else {
            /**
             * Create a new rating.
             */
            ProjectRating::create([
                ...$validated,
                'user_id' => $request->user()->id
            ]);
        }

        return response()->json([
            'numberOfRatings' => $project->ratings()->count() ?? 0,
            'averageRating' => $project->ratings()->avg('rating')
        ]);
    }
}
