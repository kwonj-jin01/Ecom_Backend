<?php

namespace Database\Factories;

use App\Models\About;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AboutFactory extends Factory
{
    protected $model = About::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'product_id' => Product::factory(),
            'content' => $this->faker->paragraphs(3, true),
        ];
    }
}
