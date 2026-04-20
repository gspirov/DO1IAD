<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Throwable;

class ProjectCommentsController extends Controller
{
    /**
     * Store a new comment for a specific project.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'comment' => ['required', 'string', 'min:3', 'max:1000']
        ]);

        /**
         * Get project from the request and if exist then check if the user is authorized to comment on it.
         */
        $project = Project::find($validated['project_id']);

        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        if (!Gate::allows('comment', $project)) {
            abort(403);
        }

        $comment = ProjectComment::create([
            ...$validated,
            'user_id' => $request->user()->id
        ]);

        return response()->json([
            'id' => $comment->id,
            'comment' => $comment->comment,
            'created_at' => $comment->created_at->format('Y-m-d H:i'),
            'username' => $comment->user->username,
            'totalCount' => $project->comments()->count()
        ]);
    }

    /**
     * Delete a specific comment from a project.
     */
    public function delete(ProjectComment $comment): JsonResponse
    {
        if (!Gate::allows('deleteComment', [$comment->project, $comment])) {
            abort(403);
        }

        try {
            $comment->delete();
            $success = true;
        } catch (Throwable) {
            $success = false;
        }

        return response()
               ->json(
                   [
                       'success' => $success,
                       'totalCount' => $comment->project->comments()->count()
                   ],
                   $success ? 200 : 400
               );
    }
}
