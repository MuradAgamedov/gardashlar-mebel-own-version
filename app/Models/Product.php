<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_category_id',
        'view_count',
        'order',
        'status',
        'discount_percent',
        'price'
    ];

    protected $casts = [
        'view_count' => 'integer',
        'order' => 'integer',
        'status' => 'boolean',
        'discount_percent' => 'decimal:2',
        'price' => 'decimal:2'
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
        return $this->hasMany(ProductFavorite::class);
    }

    /**
     * Get the compares for this product
     */
    public function compares()
    {
        return $this->hasMany(ProductCompare::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeWithDiscount($query)
    {
        return $query->where('discount_percent', '>', 0);
    }

    // Helper methods
    public function translate($languageId = null)
    {
        if (!$languageId) {
            $languageId = Language::where('is_default', true)->first()->id ?? 1;
        }
        
        return $this->translates()->where('language_id', $languageId)->first();
    }

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
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (!isset($product->order)) {
                $maxOrder = static::max('order');
                $product->order = $maxOrder + 1;
            }
        });
    }


    // User modelinə əlavə etmək lazım deyil, Product modelinə əlavə olunur

// Product modelində əlavə et
public function apiData($languageId = null, $colorId = null)
{
    if (!$languageId) {
        $languageId = app()->getLocale();
    }

    // Tərcümə
    $translation = $this->translates()
        ->where('language_id', $languageId)
        ->first();

    // Product types
    $productTypes = $this->types->map(function ($type) use ($languageId) {
        $tr = $type->translates()->where('language_id', $languageId)->first();
        return [
            'id' => $type->id,
            'title' => $tr ? $tr->title : ''
        ];
    });

    // Product details
    $productDetails = $this->details()->orderBy('order')->get()->map(function ($detail) use ($languageId) {
        $tr = $detail->translates()->where('language_id', $languageId)->first();
        return [
            'id' => $detail->id,
            'keyword' => $tr ? $tr->keyword : '',
            'value' => $tr ? $tr->value : ''
        ];
    })->filter(fn($d) => $d['keyword'] && $d['value'])->values();

    // Product detail values
    $productDetailValues = $this->detailValues()->orderBy('order')->get()->map(function ($dv) use ($languageId) {
        $tr = $dv->translates()->where('language_id', $languageId)->first();
        $details = $dv->details()->orderBy('order')->get()->map(function ($detail) use ($languageId) {
            $dtr = $detail->translates()->where('language_id', $languageId)->first();
            return [
                'id' => $detail->id,
                'keyword' => $dtr ? $dtr->keyword : '',
                'value' => $dtr ? $dtr->value : ''
            ];
        })->filter(fn($d) => $d['keyword'] && $d['value'])->values();

        return [
            'id' => $dv->id,
            'keyword' => $tr ? $tr->keyword : '',
            'details' => $details
        ];
    });

    // Main (card) image
    $cardImageModel = $this->images()->where('status', true)->where('is_main', true)->first();
    $cardImage = $cardImageModel ? [
        'id' => $cardImageModel->id,
        'image_url' => $cardImageModel->image ? asset('storage/'.$cardImageModel->image) : null,
        'color_id' => $cardImageModel->product_color_id ?? null,
        'order' => $cardImageModel->order
    ] : null;

    // Colors
    $availableColors = $this->images()->whereNotNull('product_color_id')->pluck('product_color_id')->unique()->values();
    $availableColorDetails = [];
    if ($availableColors->isNotEmpty()) {
        $availableColorDetails = ProductColor::whereIn('id', $availableColors)->get()->map(function ($color) {
            return [
                'id' => $color->id,
                'color_code' => $color->color_code
            ];
        });
    }

    // Category & Parent Slug məntiqi
    $categorySlug = null;
    $parentSlug = null;

    if ($this->categories->isNotEmpty()) {
        $category = $this->categories->first(); // əsas category
        $catTranslation = $category->translates()->where('language_id', $languageId)->first();
        $categorySlug = $catTranslation ? $catTranslation->slug : null;

        if ($category->parent_id) {
            $parentCategory = ProductCategory::find($category->parent_id);
            if ($parentCategory) {
                $parentTr = $parentCategory->translates()->where('language_id', $languageId)->first();
                $parentSlug = $parentTr ? $parentTr->slug : null;
            }
        }
    }

    // Discount qiymət
    $discountPrice = null;
    if ($this->discount_percent > 0) {
        $discountPrice = $this->price - ($this->price * $this->discount_percent / 100);
    }

    return [
        'id' => $this->id,
        'title' => $translation ? $translation->title : '',
        'slug' => $translation ? $translation->slug : '',
        'price' => $this->price,
        'discount_price' => $discountPrice,
        'discount_percent' => $this->discount_percent,
        'view_count' => $this->view_count,
        'product_types' => $productTypes,
        'product_details' => $productDetails,
        'product_detail_values' => $productDetailValues,
        'available_colors' => $availableColorDetails,
        'card_image' => $cardImage,
        'category_slug' => $categorySlug,
        'parent_slug' => $parentSlug
    ];
}







}
