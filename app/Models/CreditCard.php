<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use App\Traits\HasStatusAndOrder;
    protected $fillable = [
        'title',
        'order',
        'image',
        'months'
    ];

    /**
     * Scope a query to order by the order column.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    
}
