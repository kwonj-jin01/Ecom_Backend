<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DeliveryFactory extends Factory
{
    protected $model = Delivery::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'order_id' => Order::factory(),
            'adresse_livraison' => $this->faker->address,
            'transporteur' => $this->faker->company,
            'statut' => $this->faker->randomElement(['en_preparation', 'en_transit', 'livre', 'retarde']),
            'date_estimee' => $this->faker->dateTimeBetween('now', '+1 week'),
            'date_livraison_reelle' => $this->faker->optional()->dateTimeBetween('now', '+2 weeks'),
        ];
    }
}
