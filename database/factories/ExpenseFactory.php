<?php

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(4),
            'amount' => $this->faker->randomFloat(2, 5, 500), // de 5 à 500
        ];
    }
}
