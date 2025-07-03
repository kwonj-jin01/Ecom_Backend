<?php

namespace Database\Factories;

use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'product_id' => Product::factory(),
            'url' => $this->faker->imageUrl(600, 600, 'fashion'),
        ];
    }
}
