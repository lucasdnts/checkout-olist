<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plano;
use App\Models\Cupom;
use Carbon\Carbon;

class CupomController extends Controller
{
    /**
     * Valida um cupom e retorna o preview do checkout.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     **/
    
    public function validar(Request $request)
    {
        // dados de entrada
        $dados = $request->validate([
            'code' => 'required|string',
            'plan_id' => 'required|integer|exists:planos,id',
        ]);

        $codigoCupom = $dados['code'];
        $planoId = $dados['plan_id'];
        $cupom = Cupom::where('code', $codigoCupom)->first();
        $plano = Plano::find($planoId);


        // Cupom existe?
        if (!$cupom) {
            return response()->json(['valid' => false, 'message' => 'Cupom não encontrado.'], 404);
        }

        // Cupom ativo?
        if (!$cupom->is_active) {
            return response()->json(['valid' => false, 'message' => 'Este cupom não está mais ativo.'], 422);
        }

        // está na vigência? 
        if ($cupom->valid_until && $cupom->valid_until < Carbon::now()) {
            return response()->json(['valid' => false, 'message' => 'Este cupom expirou.'], 422);
        }

        // uso foi atingido?
        if ($cupom->usage_limit !== null && $cupom->current_usage >= $cupom->usage_limit) {
            return response()->json(['valid' => false, 'message' => 'Este cupom atingiu o limite de usos.'], 422);
        }

        // PLANO ESPECÍFICO?
        if ($cupom->plan_id !== null && $cupom->plan_id != $plano->id) {
            return response()->json([
                'valid' => false, 
                'message' => 'Este cupom não é válido para o plano selecionado.'
            ], 422);
        }

        // PERIODICIDADE?
        if ($cupom->compatible_periodicity !== null && $cupom->compatible_periodicity != $plano->periodicity) {
            return response()->json([
                'valid' => false, 
                'message' => 'Este cupom é válido apenas para planos ' . $cupom->compatible_periodicity . '.'
            ], 422);
        }

        $valoresCalculados = $this->calcularDesconto($plano, $cupom);

        return response()->json([
            'valid' => true,
            'message' => 'Cupom aplicado com sucesso!',
            'values' => $valoresCalculados,
            'coupon' => [ 
                'code' => $cupom->code,
                'discount_type' => $cupom->discount_type,
                'discount_value' => $cupom->discount_value,
            ]
        ]);
    }

    /**
     * Calcula o valor do desconto e o novo total.
     *
     * @param \App\Models\Plano 
     * @param \App\Models\Cupom 
     * @return array
     */
    private function calcularDesconto(Plano $plano, Cupom $cupom): array
    {
        $subtotal = $plano->price_in_cents;
        $desconto = 0;

        if ($cupom->discount_type === 'percentage') {
            // half-up
            $desconto = (int) round(
                ($subtotal * $cupom->discount_value) / 100, 
                0, 
                PHP_ROUND_HALF_UP
            );
        } 
        elseif ($cupom->discount_type === 'fixed') {
            $desconto = $cupom->discount_value;
        }

        $total = $subtotal - $desconto;
        if ($total < 0) {
            $total = 0;
            $desconto = $subtotal; 
        }

        return [
            'subtotal' => $subtotal,
            'discount' => $desconto,
            'total' => $total,
        ];
    }
}