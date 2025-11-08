<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plano;

class PlanoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        $planos = [
            [
                'name' => 'BASIC Mensal',
                'slug' => 'BASIC_MONTHLY',
                'price_in_cents' => 4990,
                'periodicity' => 'monthly',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PRO Mensal',
                'slug' => 'PRO_MONTHLY',
                'price_in_cents' => 9990,
                'periodicity' => 'monthly',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PRO Anual',
                'slug' => 'PRO_YEARLY',
                'price_in_cents' => 99900,
                'periodicity' => 'yearly',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($planos as $plano) {
            Plano::updateOrCreate(
                ['slug' => $plano['slug']],
                $plano
            );
        }
    }
}