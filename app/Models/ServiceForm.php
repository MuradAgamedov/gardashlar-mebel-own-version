<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'mobile',
        'email',
        'message',
        'file',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'service_id' => 'integer',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Relationships
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Scopes
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        return $this->file ? asset('storage/' . $this->file) : null;
    }

    public function getFileNameAttribute()
    {
        return $this->file ? basename($this->file) : null;
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }
}
