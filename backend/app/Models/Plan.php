<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    
    protected $table = 'plans';
    protected $fillable = [
        'name',
        'slug',
        'price_in_cents',
        'periodicity',
    ];
    protected $casts = [
        'price_in_cents' => 'integer',
    ];
}