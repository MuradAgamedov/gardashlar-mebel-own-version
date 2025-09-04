<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacancyForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_id',
        'name',
        'mobile',
        'email',
        'file',
        'message',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    /**
     * Get the vacancy that owns this form
     */
    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function getFileUrlAttribute()
    {
        return $this->file ? asset('storage/' . $this->file) : null;
    }

    public function getFileNameAttribute()
    {
        return $this->file ? basename($this->file) : null;
    }
}
