<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialNetwork extends Model
{
    use HasFactory, App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'type',
        'name',
        'icon',
        'url',
        'order',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get predefined social network types
     */
    public static function getTypes(): array
    {
        return [
            'facebook' => [
                'name' => 'Facebook',
                'icon' => 'fab fa-facebook',
                'color' => '#1877F2'
            ],
            'instagram' => [
                'name' => 'Instagram',
                'icon' => 'fab fa-instagram',
                'color' => '#E4405F'
            ],
            'twitter' => [
                'name' => 'Twitter',
                'icon' => 'fab fa-twitter',
                'color' => '#1DA1F2'
            ],
            'linkedin' => [
                'name' => 'LinkedIn',
                'icon' => 'fab fa-linkedin',
                'color' => '#0A66C2'
            ],
            'youtube' => [
                'name' => 'YouTube',
                'icon' => 'fab fa-youtube',
                'color' => '#FF0000'
            ],
            'tiktok' => [
                'name' => 'TikTok',
                'icon' => 'fab fa-tiktok',
                'color' => '#000000'
            ],
            'telegram' => [
                'name' => 'Telegram',
                'icon' => 'fab fa-telegram',
                'color' => '#0088CC'
            ],
            'whatsapp' => [
                'name' => 'WhatsApp',
                'icon' => 'fab fa-whatsapp',
                'color' => '#25D366'
            ],
            'viber' => [
                'name' => 'Viber',
                'icon' => 'fab fa-viber',
                'color' => '#7360F2'
            ],
            'snapchat' => [
                'name' => 'Snapchat',
                'icon' => 'fab fa-snapchat',
                'color' => '#FFFC00'
            ],
            'pinterest' => [
                'name' => 'Pinterest',
                'icon' => 'fab fa-pinterest',
                'color' => '#BD081C'
            ],
            'reddit' => [
                'name' => 'Reddit',
                'icon' => 'fab fa-reddit',
                'color' => '#FF4500'
            ],
            'discord' => [
                'name' => 'Discord',
                'icon' => 'fab fa-discord',
                'color' => '#5865F2'
            ],
            'twitch' => [
                'name' => 'Twitch',
                'icon' => 'fab fa-twitch',
                'color' => '#9146FF'
            ],
            'github' => [
                'name' => 'GitHub',
                'icon' => 'fab fa-github',
                'color' => '#181717'
            ]
        ];
    }

    /**
     * Get type info
     */
    public function getTypeInfoAttribute()
    {
        $types = self::getTypes();
        return $types[$this->type] ?? null;
    }

    /**
     * Get display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->name ?: ($this->type_info['name'] ?? $this->type);
    }

    /**
     * Get display icon
     */
    public function getDisplayIconAttribute()
    {
        return $this->icon ?: ($this->type_info['icon'] ?? 'fas fa-link');
    }

    /**
     * Get color
     */
    public function getColorAttribute()
    {
        return $this->type_info['color'] ?? '#6c757d';
    }

    /**
     * Scope for active social networks
     */

    /**
     * Scope for ordered social networks
     */

    /**
     * Get social networks by types
     */
    public static function getByTypes(array $types)
    {
        return self::active()
            ->whereIn('type', $types)
            ->ordered()
            ->get();
    }

    /**
     * Get social network by type
     */
    public static function getByType(string $type)
    {
        return self::active()
            ->where('type', $type)
            ->first();
    }

    /**
     * Check if social network exists by type
     */
    public static function existsByType(string $type): bool
    {
        return self::active()->where('type', $type)->exists();
    }

    /**
     * Get all available types
     */
    public static function getAvailableTypes(): array
    {
        return self::active()->pluck('type')->toArray();
    }

    /**
     * Get social networks as array for frontend
     */
    public static function getForFrontend(): array
    {
        return self::active()
            ->ordered()
            ->get()
            ->map(function ($socialNetwork) {
                return [
                    'type' => $socialNetwork->type,
                    'url' => $socialNetwork->url
                ];
            })
            ->toArray();
    }
}
