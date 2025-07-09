<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        Expense::factory()->count(30)->create(); // 30 dépenses aléatoires
    }
}
