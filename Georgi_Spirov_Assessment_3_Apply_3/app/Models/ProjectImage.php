<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Represents an image associated with a project.
 * Handles the storage and deletion of images across the application.
 */
class ProjectImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'project_id',
        'path'
    ];

    /**
     * Establishes a belongs-to relationship with the Project model.
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Automatically deletes the associated image file from storage when the image model is deleted.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::deleting(function ($image) {
            if ($image->path) {
                Storage::disk('public')->delete($image->path);
            }
        });
    }
}
