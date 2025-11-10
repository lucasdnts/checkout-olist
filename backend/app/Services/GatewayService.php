<?php

namespace App\Services;

use Illuminate\Support\Str;

class GatewayService
{

    public function processPayment(string $cardNumber, int $amountInCents): array
    {
        // aprovar cartões que começam com 5
        if (Str::startsWith($cardNumber, '5')) {
            return $this->gatewayResponse(true, 'Pagamento aprovado.');
        }

        // negar cartões que começam com 4
        if (Str::startsWith($cardNumber, '4')) {
            throw new \Exception('Pagamento negado, contate seu banco.');
        }

        // randomizar: 70% aprova / 30% nega
        if (Str::startsWith($cardNumber, '3')) {
            $chance = rand(1, 100);
            if ($chance <= 70) {
                return $this->gatewayResponse(true, 'Pagamento aprovado.');
            } else {
                throw new \Exception('Pagamento negado, contate seu banco.');
            }
        }

        // Outros cartões
        throw new \Exception('Número de cartão inválido, verifique o número do cartão.');
    }

    /**
     * formatar a resposta do gateway.
     */
    private function gatewayResponse(bool $sucesso, string $mensagem): array
    {
        return [
            'status' => $sucesso ? 'sucesso' : 'falha',
            'message' => $mensagem,
            'gateway_transaction_id' => $sucesso ? 'sandbox_' . Str::uuid() : null,
        ];
    }
}