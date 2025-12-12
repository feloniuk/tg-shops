<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('plans')->insert([
            [
                'name' => 'No Plan',
                'max_shops' => 1,
                'max_products' => 10,
                'ai_enabled' => false,
                'price' => 0.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Base',
                'max_shops' => 3,
                'max_products' => 100,
                'ai_enabled' => false,
                'price' => 9.99,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pro',
                'max_shops' => 10,
                'max_products' => PHP_INT_MAX,
                'ai_enabled' => true,
                'price' => 29.99,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}