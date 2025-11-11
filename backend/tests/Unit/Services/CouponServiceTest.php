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

    protected function setUp(): void
    {
        parent::setUp();
        $this->couponService = new CouponService();

        
        $this->planoBase = new Plan([
            'name' => 'Plano Teste',
            'price_in_cents' => 10000 //100,00
        ]);
    }

    public function calculates_percentage_discount()
    {
        $cupom = new Coupon(['discount_type' => 'percentage', 'discount_value' => 10]);

        $valores = $this->couponService->calculateDiscount($this->planoBase, $cupom);

        $this->assertEquals(10000, $valores['subtotal']);
        $this->assertEquals(1000, $valores['discount']); 
        $this->assertEquals(9000, $valores['total']);
    }

    public function calculates_fixed_discount()
    {
        $cupom = new Coupon(['discount_type' => 'fixed', 'discount_value' => 1500]);

        $valores = $this->couponService->calculateDiscount($this->planoBase, $cupom);

        $this->assertEquals(10000, $valores['subtotal']);
        $this->assertEquals(1500, $valores['discount']);
        $this->assertEquals(8500, $valores['total']);
    }

    public function handles_half_up_rounding()
    {
        $planoQuebrado = new Plan(['price_in_cents' => 4990]);
        $cupom = new Coupon(['discount_type' => 'percentage', 'discount_value' => 10]);

        $valores = $this->couponService->calculateDiscount($planoQuebrado, $cupom);

        $planoQuebrado = new Plan(['price_in_cents' => 4995]);
        $valores = $this->couponService->calculateDiscount($planoQuebrado, $cupom);

        $this->assertEquals(500, $valores['discount']);
        $this->assertEquals(4495, $valores['total']);
    }

    public function total_is_never_negative()
    {
        $cupom = new Coupon(['discount_type' => 'fixed', 'discount_value' => 20000]);

        $valores = $this->couponService->calculateDiscount($this->planoBase, $cupom);

        $this->assertEquals(10000, $valores['subtotal']);
        $this->assertEquals(10000, $valores['discount']); 
        $this->assertEquals(0, $valores['total']);
    }
}