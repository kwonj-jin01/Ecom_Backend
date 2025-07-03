<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanActivity>
 */
class LoanActivityFactory extends Factory
{
    public function definition()
    {
        return [
            'day' => $this->faker->dayOfWeek(),
            'customer_name' => $this->faker->name(),
            'amount' => $this->faker->numberBetween(10000, 500000),
            'loan_type' => $this->faker->randomElement(['Salary Loan', 'Quick Loan', 'Business Loan']),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'time' => $this->faker->time('H:i:s'),
            'status' => $this->faker->randomElement(['disbursed', 'received', 'pending']),
        ];
    }
}
