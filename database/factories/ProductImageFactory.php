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
        $images = [
            'https://images.pexels.com/photos/2294361/pexels-photo-2294361.jpeg',
            'https://images.pexels.com/photos/2983464/pexels-photo-2983464.jpeg',
            'https://images.pexels.com/photos/2983461/pexels-photo-2983461.jpeg',
            'https://images.pexels.com/photos/2983462/pexels-photo-2983462.jpeg',
            'https://images.pexels.com/photos/298863/pexels-photo-298863.jpeg',
            'https://images.pexels.com/photos/994517/pexels-photo-994517.jpeg',
        ];

        return [
            'id' => Str::uuid(),
            'product_id' => Product::factory(),
            'url' => $this->faker->randomElement($images),
        ];
    }
}
