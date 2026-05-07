<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'full_name',
        'phone',
        'email',
        'password',
        'fin',
        'google_id',
        'pending_email',
        'pending_email_token',
        'pending_email_expires_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pending_email_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user's favorite products
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the user's compared products
     */
    public function compares()
    {
        return $this->hasMany(ProductCompare::class);
    }

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute()
    {
        return trim($this->name . ' ' . $this->surname);
    }

    /**
     * Scope for verified users
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // User modelində əlavə et
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
    public function roluteeteActions()
    {
        return $this->hasMany(UserRoluteeteAction::class);
    }
}
