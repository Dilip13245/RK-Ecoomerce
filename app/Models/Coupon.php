<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'type',
        'value',
        'min_amount',
        'max_discount',
        'valid_from',
        'valid_until',
        'is_active',
        'is_deleted',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('is_deleted', false)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now());
    }

    public function calculateDiscount($amount)
    {
        if ($amount < $this->min_amount) {
            return 0;
        }

        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;
            return $this->max_discount ? min($discount, $this->max_discount) : $discount;
        }

        return $this->value;
    }
}