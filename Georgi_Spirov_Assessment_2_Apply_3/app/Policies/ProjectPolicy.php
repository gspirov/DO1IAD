<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user/guest can view particular project.
     * Everyone can view the project.
     */
    public function view(?User $user, Project $project): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create projects.
     * Every user can create projects.
     */
    public function create(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the project.
     * Only the owner of the project can update it.
     */
    public function update(?User $user, Project $project): bool
    {
        return $project->user_id === $user?->id;
    }

    /**
     * Determine whether the user can delete the project.
     * Only the owner of the project can delete it.
     */
    public function delete(?User $user, Project $project): bool
    {
        return $project->user_id === $user?->id;
    }

    /**
     * Determine whether the user can rate the project.
     * Allowed only when user is not the owner of the project.
     */
    public function rate(?User $user, Project $project): bool
    {
       return null !== $user && $project->user_id !== $user->id;
    }

    /**
     * Determine whether the user can add comment to the project.
     * Allowed only when user is not the owner of the project.
     */
    public function comment(?User $user, Project $project): bool
    {
        return null !== $user && $project->user_id !== $user->id;
    }

    /**
     * Determine whether the user can delete comment from project.
     * Allowed only when user is the owner of the project or the comment is his own.
     */
    public function deleteComment(?User $user, Project $project, ProjectComment $projectComment): bool
    {
        return null !== $user &&
               $projectComment->project_id === $project->id &&
               (
                   // User can delete his own comments
                   $projectComment->user_id === $user->id ||
                   // Owner of the product can delete any comments
                   $project->user_id === $user->id
               );
    }

    /**
     * Determine whether the user can add the project to favourites.
     * Allowed only when user is not the owner of the project.
     */
    public function addToFavourites(?User $user, Project $project): bool
    {
        return null !== $user && $project->user_id !== $user->id;
    }
}
