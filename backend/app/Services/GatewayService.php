<?php

namespace App\Services;

use Illuminate\Support\Str;

class GatewayService
{

    public function processPayment(string $cardNumber, int $amountInCents): array
    {
        if (Str::startsWith($cardNumber, '5')) {
            return $this->gatewayResponse(true, 'Pagamento aprovado.');
        }

        if (Str::startsWith($cardNumber, '4')) {
            throw new \Exception('Pagamento negado, contate seu banco.');
        }

        if (Str::startsWith($cardNumber, '3')) {
            $chance = rand(1, 100);
            if ($chance <= 70) {
                return $this->gatewayResponse(true, 'Pagamento aprovado.');
            } else {
                throw new \Exception('Pagamento negado, contate seu banco.');
            }
        }

        throw new \Exception('Número de cartão inválido, verifique o número do cartão.');
    }

    private function gatewayResponse(bool $sucesso, string $mensagem): array
    {
        return [
            'status' => $sucesso ? 'sucesso' : 'falha',
            'message' => $mensagem,
            'gateway_transaction_id' => $sucesso ? 'sandbox_' . Str::uuid() : null,
        ];
    }
}