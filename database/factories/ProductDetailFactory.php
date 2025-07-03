<?php

namespace Database\Factories;

use App\Models\ProductDetail;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductDetailFactory extends Factory
{
    protected $model = ProductDetail::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'product_id' => Product::factory(),
            'label' => $this->faker->randomElement(['Matière', 'Origine', 'Entretien', 'Composition']),
            'value' => $this->faker->sentence(),
        ];
    }
}
