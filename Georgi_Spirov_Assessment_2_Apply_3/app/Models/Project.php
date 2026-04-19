<?php

namespace App\Models;

use App\Enums\ProjectPhaseEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * This model represents a project and its associated data, including its relationships
 * with users, comments, ratings, images, and users who have put to favourites the project.
 * It also handles cleanup logic for associated resources on project deletion.
 */
class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'short_description',
        'phase',
        'user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'phase' => ProjectPhaseEnum::class,
    ];

    /**
     * Get the user that owns the project.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments associated with the project.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ProjectComment::class);
    }

    /**
     * Get the ratings associated with the project.
     *
     * @return HasMany
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(ProjectRating::class);
    }

    /**
     * Get the images associated with the project.
     *
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class);
    }


    /**
     * The users who have favourite the project.
     *
     * @return BelongsToMany
     */
    public function favouriteByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_project_favourites')
                    ->withTimestamps();
    }

    /**
     * Deletes all associated image files when a project is deleted.
     *
     * @return void
     */
    protected static function booted(): void
    {
        /**
         * Delete all images (as files) associated with the project when the project is deleted.
         */
        static::deleting(function ($project) {
            foreach ($project->images as $image) {
                Storage::disk('public')->delete($image->path);
            }
        });
    }
}
