<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Coupon;
use App\Services\CouponService;
use App\Exceptions\InvalidCouponException;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function __construct(protected CouponService $couponService)
    {
    }

    public function validateCoupon(Request $request)
    {
        $dados = $request->validate([
            'code' => 'required|string',
            'plan_id' => 'required|integer|exists:plans,id',
        ]);

        try {
            $coupon = Coupon::where('code', $dados['code'])->first();
            $plan = Plan::find($dados['plan_id']);

            if (!$coupon) {
                throw new InvalidCouponException('Cupom não encontrado.');
            }
            $this->couponService->validateCoupon($coupon, $plan);
            $valores = $this->couponService->calculateDiscount($plan, $coupon);

            return response()->json([
                'valid' => true,
                'message' => 'Cupom aplicado com sucesso!',
                'values' => $valores,
                'coupon' => [
                    'code' => $coupon->code,
                    'discount_type' => $coupon->discount_type,
                    'discount_value' => $coupon->discount_value,
                ]
            ]);

        } catch (InvalidCouponException $e) {

            return response()->json([
                'valid' => false,
                'message' => $e->getMessage()
            ], 422); 

        } catch (\Exception $e) {
            Log::error('Erro ao validar cupom: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'message' => 'Erro interno ao processar o cupom.'
            ], 500);
        }
    }
}