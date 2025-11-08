<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GatewayMockController extends Controller
{
    public function processPayment(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string|numeric',
            'amount_in_cents' => 'required|integer|min:0',
        ]);

        $cardNumber = $request->input('card_number');

        // aprovar cartoes que começam com 5
        if (Str::startsWith($cardNumber, '5')) {
            return $this->gatewayResponse(true, 'Pagamento aprovado.');
        }

        // negar cartões que começam com 4
        if (Str::startsWith($cardNumber, '4')) {
            return $this->gatewayResponse(false, 'Pagamento negado.');
        }

        // randomizar: 70% aprova / 30% nega
        if (Str::startsWith($cardNumber, '3')) {
            $chance = rand(1, 100);
            if ($chance <= 70) {
                return $this->gatewayResponse(true, 'Pagamento aprovado.');
            } else {
                return $this->gatewayResponse(false, 'Pagamento negado.');
            }
        }

        
        return $this->gatewayResponse(false, 'Número de cartão inválido, verifique o número do cartão.');
    }

    /**
     * formatar a resposta do gateway
     */
    private function gatewayResponse(bool $sucesso, string $mensagem)
    {
        $status = $sucesso ? 'sucesso' : 'falha';

        return response()->json([
            'status' => $status,
            'message' => $mensagem,
            'gateway_transaction_id' => $sucesso ? 'sim_' . Str::uuid() : null,
        ], $sucesso ? 200 : 400);
    }
}