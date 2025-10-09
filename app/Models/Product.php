<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'price',
        'discounted_price',
        'min_quantity',
        'description',
        'images',
        'specifications',
        'category_id',
        'subcategory_id',
        'rating_average',
        'rating_count',
        'is_active',
        'is_deleted',
    ];

    protected $casts = [
        'images' => 'array',
        'specifications' => 'array',
        'price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'rating_average' => 'decimal:2',
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function colors()
    {
        return $this->hasMany(ProductColor::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function wishlistUsers()
    {
        return $this->hasMany(UserWishlist::class);
    }

    public function cartItems()
    {
        return $this->hasMany(UserCart::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_deleted', false);
    }
}