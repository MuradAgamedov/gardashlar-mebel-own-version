<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_type_id',
        'email',
        'order',
        'status',
    ];

    protected $casts = [
        'email_type_id' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the email type that owns this email
     */
    public function emailType()
    {
        return $this->belongsTo(EmailType::class);
    }

    /**
     * Scope for active emails
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for ordered emails
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Auto-increment order when creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($email) {
            if (!isset($email->order)) {
                $maxOrder = static::max('order');
                $email->order = $maxOrder + 1;
            }
        });
    }
} 