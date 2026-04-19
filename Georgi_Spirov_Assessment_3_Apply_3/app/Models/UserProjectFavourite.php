<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Represents the user's favorite project functionality.
 * This model establishes a relationship between users and their favorite projects.
 *
 * Relationships:
 * - User: The user who put the project into favorites.
 * - Project: The project that is put by the user as favourite.
 */
class UserProjectFavourite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
