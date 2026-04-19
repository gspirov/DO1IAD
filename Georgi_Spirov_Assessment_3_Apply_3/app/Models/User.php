<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

/**
 * User model representing the authenticated application user.
 *
 * This class extends Illuminate\Foundation\Auth\User, providing
 * authentication functionalities. The User model also implements
 * MustVerifyEmail and CanResetPassword interfaces for email verification
 * and password reset capabilities, respectively.
 *
 * Key Features:
 * - Mass assignable attributes: username, email, password, avatar
 * - Relationships: Many-to-many relationship with Project through favourites
 * - Custom deletion logic: Deletes stored avatar image when a user record is removed
 */
class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Define a one-to-many relationship between User and Project models.
     *
     * This relationship allows a user to create multiple projects, where the user
     * acts as the owner of the associated projects.
     *
     * @return HasMany
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Define a many-to-many relationship between User and Project models.
     *
     * This relationship represents the "favourites" functionality, where
     * a user can mark multiple projects as favourites. The pivot table
     * 'user_project_favourites' is used for managing this connection.
     *
     * @return BelongsToMany
     */
    public function favourites(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'user_project_favourites')
                    ->withTimestamps();
    }

    /**
     * Define a one-to-many relationship between User and ProjectComment models.
     *
     * A user can create multiple comments across different projects.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ProjectComment::class);
    }

    /**
     * Automatically deletes the avatar image file stored on the disk
     * when the user record is deleted to maintain file consistency
     * and prevent orphaned files in the storage.
     */
    protected static function booted(): void
    {
        static::deleting(function ($user) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
        });
    }
}
