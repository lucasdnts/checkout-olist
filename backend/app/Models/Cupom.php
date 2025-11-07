<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cupom extends Model
{
    use HasFactory;

    protected $table = 'cupons';

    protected $fillable = [
        'code',
        'is_active',
        'discount_type',
        'discount_value',
        'valid_until',
        'usage_limit',
        'current_usage',
        'compatible_periodicity',
        'plan_id',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'discount_value' => 'integer',
        'valid_until' => 'datetime',
        'usage_limit' => 'integer',
        'current_usage' => 'integer',
    ];

    public function plano(): BelongsTo
    {
        return $this->belongsTo(Plano::class, 'plan_id');
    }
}