<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, \App\Traits\Translatable, \App\Traits\HasStatusAndOrder;

    protected $fillable = [
        'project_category_id',
        'view_count',
        'order',
        'status',
        'price',
        'discount_price', // əlavə edildi
    ];
    
    protected $casts = [
        'view_count' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2', // əlavə edildi
    ];

    // Relationships
    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category');
    }

    public function types()
    {
        return $this->belongsToMany(ProductType::class, 'product_product_type');
    }

    public function projectCategory()
    {
        return $this->belongsTo(ProjectCategory::class);
    }

    /**
     * Get homepage type product entries for this product
     */
    public function homePageTypeProducts()
    {
        return $this->hasMany(HomePageTypeProduct::class);
    }

    public function translates()
    {
        return $this->hasMany(ProductTranslate::class, 'product_id');
    }

    public function tags()
    {
        return $this->belongsToMany(ProductTag::class, 'product_product_tag', 'product_id', 'product_tag_id');
    }

    public function accordions()
    {
        return $this->hasMany(ProductAccordion::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get all colors available for this product through images
     */
    public function colors()
    {
        return $this->hasManyThrough(
            ProductColor::class,
            ProductImage::class,
            'product_id', // Foreign key on ProductImage table
            'id', // Foreign key on ProductColor table
            'id', // Local key on Product table
            'product_color_id' // Local key on ProductImage table
        )->distinct();
    }

    public function details()
    {
        return $this->hasMany(ProductDetail::class);
    }

    public function detailValues()
    {
        return $this->hasMany(ProductDetailValue::class);
    }

    /**
     * Get the favorites for this product
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the compares for this product
     */
    public function compares()
    {
        return $this->hasMany(ProductCompare::class);
    }

    /**
     * Get orders that contain this product
     * Since orders now use product_ids (JSON), we need a custom relationship
     */
    public function orders()
    {
        return Order::whereRaw('JSON_CONTAINS(product_ids, ?)', [json_encode($this->id)]);
    }

    // Scopes

    public function scopeWithDiscount($query)
    {
        return $query->where('discount_percent', '>', 0);
    }

    // Helper methods

    public function getTitleAttribute()
    {
        $translation = $this->translate();
        return $translation ? $translation->title : 'No title';
    }

    public function getDescriptionAttribute()
    {
        $translation = $this->translate();
        return $translation ? $translation->description : null;
    }

    public function getSlugAttribute()
    {
        $translation = $this->translate();
        return $translation ? $translation->slug : 'no-slug';
    }

    /**
     * Get company products for this product
     */
    public function companyProducts()
    {
        return $this->hasMany(CompanyProduct::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
