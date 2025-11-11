<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Coupon;
use Carbon\Carbon;
use App\Exceptions\InvalidCouponException;
class CouponService
{

    public function validateCoupon(Coupon $coupon, Plan $plan): void
    {
        if (!$coupon->is_active) {
            throw new InvalidCouponException('Este cupom não está mais ativo.');
        }

        if ($coupon->valid_until && $coupon->valid_until < Carbon::now()) {
            throw new InvalidCouponException('Este cupom expirou.');
        }

        if ($coupon->usage_limit !== null) {
            $currentUsage = $coupon->usages()->count();
            if ($currentUsage >= $coupon->usage_limit) {
                throw new InvalidCouponException('Este cupom atingiu o limite de usos.');
            }
        }

        if ($coupon->plan_id !== null && $coupon->plan_id != $plan->id) {
            throw new InvalidCouponException('Este cupom não é válido para o plano selecionado.');
        }

        if ($coupon->compatible_periodicity !== null && $coupon->compatible_periodicity != $plan->periodicity) {
            $periodicity = match ($coupon->compatible_periodicity) {
            'yearly' => 'anuais',
            'monthly' => 'mensais',
            default => strtolower($coupon->compatible_periodicity),
        };

            throw new InvalidCouponException('Este cupom é válido apenas para planos ' . $periodicity . '.');
        }
    }

    public function calculateDiscount(Plan $plan, Coupon $coupon): array
    {
        $subtotal = $plan->price_in_cents;
        $discount = 0;

        if ($coupon->discount_type === 'percentage') {
            $discount = (int) round(
                ($subtotal * $coupon->discount_value) / 100,
                0,
                PHP_ROUND_HALF_UP 
            );
        } elseif ($coupon->discount_type === 'fixed') {
            $discount = $coupon->discount_value;
        }

        $total = $subtotal - $discount;

        if ($total < 0) {
            $total = 0;
            $discount = $subtotal;
        }

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
        ];
    }
}