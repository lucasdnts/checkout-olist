<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CouponService;
use App\Models\Plan;
use App\Models\Coupon;

class CouponServiceTest extends TestCase
{
    private $couponService;
    private $planoBase;

    /**
     * ambiente de teste
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->couponService = new CouponService();

        
        $this->planoBase = new Plan([
            'name' => 'Plano Teste',
            'price_in_cents' => 10000 //100,00
        ]);
    }

    /**
     * @test
     * desconto por porcentagem
     */
    public function calculates_percentage_discount()
    {
        $cupom = new Coupon(['discount_type' => 'percentage', 'discount_value' => 10]);

        $valores = $this->couponService->calculateDiscount($this->planoBase, $cupom);

        // 10000 centavos com 10% de desconto
        $this->assertEquals(10000, $valores['subtotal']);
        $this->assertEquals(1000, $valores['discount']); // 10% de 10000
        $this->assertEquals(9000, $valores['total']);
    }

    /**
     * @test
     * cálculo de desconto fixo.
     */
    public function calculates_fixed_discount()
    {
        $cupom = new Coupon(['discount_type' => 'fixed', 'discount_value' => 1500]);

        $valores = $this->couponService->calculateDiscount($this->planoBase, $cupom);

        //  10000 centavos com 15,00 de desconto
        $this->assertEquals(10000, $valores['subtotal']);
        $this->assertEquals(1500, $valores['discount']);
        $this->assertEquals(8500, $valores['total']);
    }

    /**
     * @test
     * testa half-up
     */
    public function handles_half_up_rounding()
    {
        // plano de 49,90
        $planoQuebrado = new Plan(['price_in_cents' => 4990]);
        // 10% de desconto
        $cupom = new Coupon(['discount_type' => 'percentage', 'discount_value' => 10]);

        $valores = $this->couponService->calculateDiscount($planoQuebrado, $cupom);

        // 4995 * 10% = 499.5, que deve arredondar para 500
        $planoQuebrado = new Plan(['price_in_cents' => 4995]);
        $valores = $this->couponService->calculateDiscount($planoQuebrado, $cupom);

        $this->assertEquals(500, $valores['discount']); // 499.5 arredonda para cima
        $this->assertEquals(4495, $valores['total']);
    }

    /**
     * @test
     * se o total nunca fica negativo
     */
    public function total_is_never_negative()
    {
        $cupom = new Coupon(['discount_type' => 'fixed', 'discount_value' => 20000]);

        // plano de 10000
        $valores = $this->couponService->calculateDiscount($this->planoBase, $cupom);

        $this->assertEquals(10000, $valores['subtotal']);
        $this->assertEquals(10000, $valores['discount']); 
        $this->assertEquals(0, $valores['total']);
    }
}