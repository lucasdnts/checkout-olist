<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\Subscription;
use App\Services\CouponService;
use App\Exceptions\InvalidCouponException;
use Illuminate\Support\Facades\DB;
use App\Services\GatewayService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    // Injeta ambos os serviços
    public function __construct(
        protected CouponService $couponService,
        protected GatewayService $gatewayService
    ) {
    }

    public function processCheckout(Request $request)
    {
        // dados de entrada
        $dados = $request->validate([
            'email' => 'required|email',
            'plan_id' => 'required|integer|exists:plans,id',
            'coupon_code' => 'nullable|string', 
            'card_number' => 'required|string|numeric',
            'card_holder' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvc' => 'required|string|numeric|digits_between:3,4',
            'idempotency_key' => 'required|string|uuid',
        ]);

        // evitar duplicidade
        $subscription = Subscription::where('idempotency_key', $dados['idempotency_key'])->first();
        if ($subscription) {
            return $this->returnSubscription($subscription->id); //retorna o resultado anterior
        }

        try {
            $plano = Plan::find($dados['plan_id']);
            $cupom = null;
            $valores = [
                'subtotal' => $plano->price_in_cents,
                'discount' => 0,
                'total' => $plano->price_in_cents,
            ];

            // revalida cupom
            if ($dados['coupon_code']) {
                $cupom = Coupon::where('code', $dados['coupon_code'])->first();
                if (!$cupom) {
                    throw new InvalidCouponException('Cupom não encontrado.');
                }
            
                $this->couponService->validateCoupon($cupom, $plano);
                $valores = $this->couponService->calculateDiscount($plano, $cupom);
            }

            $gatewayData = $this->gatewayService->processPayment(
                $dados['card_number'],
                $valores['total']
            );

            $assinaturaFinal = DB::transaction(function () use ($plano, $cupom, $valores, $dados, $gatewayData) {

                // assinatura
                $assinatura = Subscription::create([
                    'plan_id' => $plano->id,
                    'plan_price_in_cents' => $plano->price_in_cents, // Congela o preco
                    'user_email' => $dados['email'],
                    'status' => 'active',
                    'idempotency_key' => $dados['idempotency_key'],
                ]);

                $cupomUso = null;
                // registra cupom se uso
                if ($cupom) {
                    $cupomUso = $assinatura->couponUsage()->create([
                        'coupon_id' => $cupom->id,
                        'user_email' => $dados['email'],
                        'discount_amount_in_cents' => $valores['discount'],
                    ]);
                }

                // transaçao
                $assinatura->transactions()->create([
                    'coupon_usage_id' => $cupomUso ? $cupomUso->id : null,
                    'gateway_status' => $gatewayData['status'],
                    'amount_paid_in_cents' => $valores['total'],
                    'gateway_transaction_id' => $gatewayData['gateway_transaction_id'],
                ]);
                
                return $assinatura;
            });

            return $this->returnSubscription($assinaturaFinal->id);

        } catch (InvalidCouponException | ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);

        } catch (\Exception $e) {
            Log::error(
                'Falha no processamento do checkout',
                [
                    'plan_id' => $dados['plan_id'] ?? null,
                    'user_email' => $dados['email'] ?? null,
                    'coupon_code' => $dados['coupon_code'] ?? null,
                    'idempotency_key' => $dados['idempotency_key'],
                    'exception_message' => $e->getMessage(),
                ]
            );
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * recuperar detalhes assinatura
     */
    public function returnSubscription($id)
    {
        $assinatura = Subscription::with(['plan', 'transactions', 'couponUsage.coupon'])
            ->find($id);

        if (!$assinatura) {
            return response()->json(['message' => 'Assinatura não encontrada.'], 404);
        }

        return response()->json($assinatura);
    }
}