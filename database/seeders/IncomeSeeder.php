<?php

namespace Database\Seeders;

use App\Models\Income;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Income::create([
            'user_id' => 1,
            'amount' => 5000.00,
            'source' => 'Salary',
            'category' => 'Employment',
            'date' => now()->subDays(10),
            'notes' => 'Monthly salary',
        ]);

        Income::create([
            'user_id' => 1,
            'amount' => 200.00,
            'source' => 'Freelance',
            'category' => 'Side Job',
            'date' => now()->subDays(5),
            'notes' => 'Web design project',
        ]);
    }
}
