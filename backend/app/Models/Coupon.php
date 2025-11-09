<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'is_active',
        'discount_type',
        'discount_value',
        'valid_until',
        'usage_limit',
        'compatible_periodicity',
        'plan_id',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'discount_value' => 'integer',
        'valid_until' => 'datetime',
        'usage_limit' => 'integer',
    ];

    //Relations
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }
}