<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'subscription_id',
        'coupon_usage_id',
        'gateway_status',
        'amount_paid_in_cents',
        'gateway_transaction_id',
    ];

    
     // Relations

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function couponUsage(): BelongsTo
    {
        return $this->belongsTo(CouponUsage::class);
    }
}