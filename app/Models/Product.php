<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, App\Traits\Translatable, App\Traits\HasStatusAndOrder;

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

    // Auto-increment order

    // User modelinə əlavə etmək lazım deyil, Product modelinə əlavə olunur

// Product modelində əlavə et
public function apiData($languageId = null, $colorId = null)
{
    // Default language
    if (!$languageId) {
        $languageId = app()->getLocale();
    }

    // Translation
    $translation = $this->translates()->where('language_id', $languageId)->first();

    // Product types
    $productTypes = $this->types->map(fn($type) => [
        'id' => $type->id,
        'title' => $type->translates()->where('language_id', $languageId)->first()?->title ?? ''
    ]);

    // Product details
    $productDetails = $this->details()->orderBy('order')->get()->map(fn($detail) => [
        'id' => $detail->id,
        'keyword' => $detail->translate($languageId)?->keyword ?? '',
        'value' => $detail->translate($languageId)?->value ?? ''
    ])->filter(fn($d) => $d['keyword'] && $d['value'])->values();

    // Images
    $images = $this->images()->where('status', true)->orderBy('order')->get()->map(fn($img) => [
        'id' => $img->id,
        'image_url' => $img->image ? asset('storage/' . $img->image) : asset('images/default_image.webp'),
        'color_id' => $img->product_color_id ?? null,
        'is_main' => $img->is_main
    ]);

    return [
        'id' => $this->id,
        'slug' => $translation?->slug ?? '',
        'title' => $translation?->title ?? '',
        'price' => $this->price,
        'discount_price' => $this->discount_price,
        'product_types' => $productTypes,
        'product_details' => $productDetails,
        'images' => $images,
        'view_count' => $this->view_count,
        'available_colors' => $this->colors->map(fn($color) => [
            'id' => $color->id,
            'color_code' => $color->color_code
        ]),
    ];
}

public function similarData($languageId = null)
{
    if (!$languageId) {
        $languageId = app()->getLocale();
    }

    // Tərcümə
    $translation = $this->translates()
        ->where('language_id', $languageId)
        ->first();

    // Məhsul tipləri
    $productTypes = $this->types->map(function ($type) use ($languageId) {
        $tr = $type->translates()->where('language_id', $languageId)->first();
        return [
            'id' => $type->id,
            'title' => $tr ? $tr->title : ''
        ];
    });

    // Əsas şəkil
    $mainImage = $this->images()
        ->where('status', true)
        ->orderBy('order')
        ->first();

    $cardImage = $mainImage && $mainImage->image
        ? asset('storage/' . $mainImage->image)
        : asset('images/default_image.webp');

    // Kategoriyalar (parent_slug və parent_title ilə birlikdə)
    $categories = $this->categories->map(function ($category) use ($languageId) {
        $tr = $category->translates()->where('language_id', $languageId)->first();

        $parentSlug = null;
        $parentTitle = null;
        if ($category->parent_id) {
            $parentCategory = ProductCategory::find($category->parent_id);
            if ($parentCategory) {
                $parentTr = $parentCategory->translates()->where('language_id', $languageId)->first();
                $parentSlug = $parentTr ? $parentTr->slug : null;
                $parentTitle = $parentTr ? $parentTr->title : null;
            }
        }

        return [
            'id' => $category->id,
            'title' => $tr ? $tr->title : '',
            'slug' => $tr ? $tr->slug : '',
            'parent_slug' => $parentSlug,
            'parent_title' => $parentTitle,
        ];
    });

    return [
        'id' => $this->id,
        'title' => $translation ? $translation->title : '',
        'slug' => $translation ? $translation->slug : '',
        'price' => $this->price,
        'discount_price' => $this->discount_price,
        'view_count' => $this->view_count,
        'card_image' => $cardImage,
        'product_types' => $productTypes,
        'categories' => $categories
    ];
}

public function comments()
{
    return $this->hasMany(Comment::class);
}

}
