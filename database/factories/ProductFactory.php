<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 10, 500);
        $discountPercentage = $this->faker->optional(0.3)->randomFloat(2, 5, 50);
        $originalPrice = $discountPercentage ? $price / (1 - $discountPercentage / 100) : $price;

        return [
            'id' => Str::uuid(),
            'title' => $this->faker->sentence(3),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->paragraph(3),
            'price' => $price,
            'original_price' => $originalPrice,
            'discount_percentage' => $this->faker->optional(0.3, 0)->randomFloat(2, 5, 50),
            'discount' => $discountPercentage ? $originalPrice - $price : 0,
            'rating' => $this->faker->randomFloat(1, 1, 5),
            'stock' => $this->faker->numberBetween(0, 100),
            'brand' => $this->faker->company(),
            'gender' => $this->faker->randomElement(['homme', 'femme']),
            'thumbnail' => $this->faker->imageUrl(300, 300, 'fashion'),
            'image' => $this->faker->imageUrl(600, 600, 'fashion'),
            'hover_image' => $this->faker->imageUrl(600, 600, 'fashion'),
            'is_new' => $this->faker->boolean(20),
            'is_best_seller' => $this->faker->boolean(15),
            'in_stock' => $this->faker->boolean(85),
            'is_on_sale' => $this->faker->boolean(30),
            'promotion' => $this->faker->optional(0.2)->sentence(),
            'category_id' => Category::factory(),
        ];
    }
}
