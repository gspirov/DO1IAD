<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Controller for handling actions related to user's favourite projects.
 */
class UserProjectFavouriteController extends Controller
{
    /**
     * Toggles the favourite status of a project for the authenticated user.
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id'
        ]);

        $project = Project::find($validated['project_id']);

        if (!Gate::allows('add-to-favourites', $project)) {
            abort(403);
        }

        Auth::user()->favourites()->toggle($validated['project_id']);

        return response()->json(['ok' => true]);
    }
}
