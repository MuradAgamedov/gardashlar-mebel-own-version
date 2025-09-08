<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'project_id' => 'integer',
    ];

    /**
     * Get the user that owns this favorite
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project that is favorited
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Check if a user has favorited a specific project
     */
    public static function isFavorited($userId, $projectId)
    {
        return static::where('user_id', $userId)
                    ->where('project_id', $projectId)
                    ->exists();
    }

    /**
     * Add a project to favorites
     */
    public static function addToFavorites($userId, $projectId)
    {
        return static::firstOrCreate([
            'user_id' => $userId,
            'project_id' => $projectId,
        ]);
    }

    /**
     * Remove a project from favorites
     */
    public static function removeFromFavorites($userId, $projectId)
    {
        return static::where('user_id', $userId)
                    ->where('project_id', $projectId)
                    ->delete();
    }
}
