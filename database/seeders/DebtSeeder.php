<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DebtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Debt::create([
            'user_id' => 1,
            'name' => 'Credit Card',
            'total_amount' => 2000.00,
            'monthly_payment' => 150.00,
            'remaining_balance' => 2000.00,
        ]);

        \App\Models\Debt::create([
            'user_id' => 1,
            'name' => 'Car Loan',
            'total_amount' => 15000.00,
            'monthly_payment' => 300.00,
            'remaining_balance' => 15000.00,
        ]);
    }
}
