<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestAPlaceForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_surname',
        'email',
        'area_sqm',
        'mobile',
        'address',
        'floor_count',
        'is_repaired',
        'has_warehouse',
        'has_parking',
        'details',
        'file',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_repaired' => 'boolean',
        'has_warehouse' => 'boolean',
        'has_parking' => 'boolean',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

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

    // Accessor methods for boolean fields
    public function getRepairStatusTextAttribute()
    {
        return $this->is_repaired ? 'Təmirli' : 'Təmirsiz';
    }

    public function getWarehouseStatusTextAttribute()
    {
        return $this->has_warehouse ? 'Bəli' : 'Xeyr';
    }

    public function getParkingStatusTextAttribute()
    {
        return $this->has_parking ? 'Bəli' : 'Xeyr';
    }
}
