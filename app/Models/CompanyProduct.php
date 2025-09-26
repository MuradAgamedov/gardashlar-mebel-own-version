<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'product_id',
        'action_id',
        'is_action_product',
        'order'
    ];

    protected $casts = [
        'is_action_product' => 'boolean',
        'order' => 'integer'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function scopeActionProducts($query)
    {
        return $query->where('is_action_product', true);
    }

    public function scopeRegularProducts($query)
    {
        return $query->where('is_action_product', false);
    }
}
