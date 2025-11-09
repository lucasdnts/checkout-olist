<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Coupon;
use Carbon\Carbon;
use App\Exceptions\InvalidCouponException;
class CouponService
{
    /**
     * @param Coupon
     * @param Plan 
     * @return void
     * @throws InvalidCouponException
     */
    public function validateCoupon(Coupon $coupon, Plan $plan): void
    {
        // cupom está ativo?
        if (!$coupon->is_active) {
            throw new InvalidCouponException('Este cupom não está mais ativo.');
        }

        // está na vigência?
        if ($coupon->valid_until && $coupon->valid_until < Carbon::now()) {
            throw new InvalidCouponException('Este cupom expirou.');
        }

        // limite de uso foi atingido?
        if ($coupon->usage_limit !== null) {
            $currentUsage = $coupon->usages()->count(); // Conta os usos reais
            if ($currentUsage >= $coupon->usage_limit) {
                throw new InvalidCouponException('Este cupom atingiu o limite de usos.');
            }
        }

        // compatível com o Plano?
        if ($coupon->plan_id !== null && $coupon->plan_id != $plan->id) {
            throw new InvalidCouponException('Este cupom não é válido para o plano selecionado.');
        }

        // compatível com a Periodicidade?
        if ($coupon->compatible_periodicity !== null && $coupon->compatible_periodicity != $plan->periodicity) {
            $periodicity = match ($coupon->compatible_periodicity) {
            'yearly' => 'anuais',
            'monthly' => 'mensais',
            default => strtolower($coupon->compatible_periodicity),
        };

            throw new InvalidCouponException('Este cupom é válido apenas para planos ' . $periodicity . '.');
        }
    }

    /**
     * Calcula o valor do desconto e o novo total.
     *
     * @param Plan 
     * @param Coupon 
     * @return array
     */
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