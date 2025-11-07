<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Plano;

class CupomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('cupons')->delete();

        $proMonthly = DB::table('planos')->where('slug', 'PRO_MONTHLY')->first();

        $cupons = [
            // 10% em qualquer periodicidade, sem limite, sem expiração
            [
                'code' => 'OFF10',
                'is_active' => true,
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'valid_until' => null,
                'usage_limit' => null,
                'current_usage' => 0,
                'compatible_periodicity' => null,
                'plan_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // R$30 no PRO mensal, válido por 5 dias, 2 usos 
            [
                'code' => 'SAVE30',
                'is_active' => true,
                'discount_type' => 'fixed',
                'discount_value' => 3000,
                'valid_until' => now()->addDays(5),
                'usage_limit' => 2,
                'current_usage' => 0,
                'compatible_periodicity' => 'monthly',
                'plan_id' => $proMonthly ? $proMonthly->id : null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 20% nos anuais, válido por 30 dias, 5 usos
            [
                'code' => 'YEAR20',
                'is_active' => true,
                'discount_type' => 'percentage',
                'discount_value' => 20, 
                'valid_until' => now()->addDays(30),
                'usage_limit' => 5,
                'current_usage' => 0,
                'compatible_periodicity' => 'yearly',
                'plan_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // qualquer periodicidade, sem limite, expirado
            [
                'code' => 'EXPIRED5',
                'is_active' => true, 
                'discount_type' => 'fixed',
                'discount_value' => 500, 
                'valid_until' => now()->subDay(),
                'usage_limit' => null,
                'current_usage' => 0,
                'compatible_periodicity' => null,
                'plan_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('cupons')->insert($cupons);
    }
}