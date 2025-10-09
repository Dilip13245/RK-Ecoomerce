<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWishlist extends Model
{
    use HasFactory;

    protected $table = 'user_wishlist';

    protected $fillable = [
        'user_id',
        'product_id',
        'is_added',
    ];

    protected $casts = [
        'is_added' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}