<?php

namespace Database\Factories;

use App\Models\ProductColor;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductColorFactory extends Factory
{
    protected $model = ProductColor::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'product_id' => Product::factory(),
            'name' => $this->faker->colorName(),
        ];
    }
}
