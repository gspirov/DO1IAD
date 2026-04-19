<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectsController
{
    /**
     * Display the details page for a specific project.
     */
    public function show(Project $project): View
    {
        /**
         * Load the user, ratings, and favouriteByUsers relationships eagerly.
         */
        $project->load([
            'user',
            'ratings.user',
            'favouriteByUsers',
        ])
        // Load the average rating for the project
        ->loadAvg('ratings', 'rating')
        // Load the count of ratings for the project
        ->loadCount('favouriteByUsers');

        $comments = $this->createCommentsCollection($project, 0);

        $currentUserRating = $project->ratings()->where('user_id', auth()->id())->first();

        return view('projects.show', [
            'project' => $project,
            'comments' => $this->mapCommentModels($comments),
            'ableToDeleteCommentsIds' => $this->getAbleToDeleteCommentsIds($comments, $project),
            'currentUserRating' => $currentUserRating ? $currentUserRating->rating : ''
        ]);
    }

    /**
     * Handle AJAX requests to fetch project comments or return a 405 if the request doesn't expect JSON.
     */
    public function comments(Request $request, Project $project): View|JsonResponse
    {
        if (!$request->expectsJson()) {
            abort(405);
        }

        $comments = $this->createCommentsCollection($project, $request->query('offset', 0));

        return response()->json($this->mapCommentModels($comments));
    }

    /**
     * Fetch a paginated collection of comments for a given project.
     */
    private function createCommentsCollection(Project $project, int $offset)
    {
        return $project->comments()
                       ->with(['user:id,username', 'project'])
                       ->skip($offset)
                       ->take(10)
                       ->get();
    }

    /**
     * Retrieve comment IDs that the current user can delete.
     */
    private function getAbleToDeleteCommentsIds($comments, Project $project)
    {
        return $comments->filter(fn($comment) => Gate::allows('deleteComment', [$project, $comment]))
                        ->pluck('id')
                        ->toArray();
    }

    /**
     * Map a collection of comment models into an array format for responses.
     */
    private function mapCommentModels($comments)
    {
        return $comments->map(fn ($comment) => [
            'id' => $comment->id,
            'comment' => $comment->comment,
            'created_at' => $comment->created_at->format('Y-m-d H:i'),
            'username' => $comment->user->username
        ]);
    }
}
