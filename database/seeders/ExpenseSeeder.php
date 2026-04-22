<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Expense::create([
            'user_id' => 1,
            'amount' => 150.00,
            'type' => 'fixed',
            'category' => 'Rent',
            'date' => now()->subDays(15),
            'notes' => 'Monthly rent payment',
        ]);

        \App\Models\Expense::create([
            'user_id' => 1,
            'amount' => 75.00,
            'type' => 'variable',
            'category' => 'Groceries',
            'date' => now()->subDays(10),
            'notes' => 'Weekly groceries',
        ]);

        \App\Models\Expense::create([
            'user_id' => 1,
            'amount' => 50.00,
            'type' => 'variable',
            'category' => 'Entertainment',
            'date' => now()->subDays(5),
            'notes' => 'Movie night',
        ]);
    }
}
