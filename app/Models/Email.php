<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory, \App\Traits\HasStatusAndOrder;

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

    /**
     * Scope for ordered emails
     */

    /**
     * Auto-increment order when creating
     */
    
} 