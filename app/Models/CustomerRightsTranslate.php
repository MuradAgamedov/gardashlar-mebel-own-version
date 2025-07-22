<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRightsTranslate extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_rights_id',
        'language_id',
        'title',
        'content',
    ];

    public function customerRights()
    {
        return $this->belongsTo(CustomerRights::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
