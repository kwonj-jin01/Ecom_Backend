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
        $brands = ['Nike', 'Adidas', 'Zara', 'H&M', 'Puma', 'Uniqlo', 'Lacoste', 'Levi\'s'];
        $genders = ['homme', 'femme'];

        $brand = $this->faker->randomElement($brands);
        $gender = $this->faker->randomElement($genders);
        $productType = $this->faker->randomElement(['T-shirt', 'Jean', 'Robe', 'Chaussures', 'Veste', 'Sweat', 'Short']);
        $name = "$productType $gender";
        $title = "$brand - $productType $gender";

        $price = $this->faker->randomFloat(2, 10, 500);
        $discountPercentage = $this->faker->optional(0.4)->randomFloat(2, 5, 50); // jusqu’à -50%
        $originalPrice = $discountPercentage ? round($price / (1 - $discountPercentage / 100), 2) : $price;
        $discount = $discountPercentage ? round($originalPrice - $price, 2) : 0;

        $images = [
            // VÊTEMENTS
            'https://images.pexels.com/photos/2983464/pexels-photo-2983464.jpeg', // T-shirt rouge
            'https://images.pexels.com/photos/5886041/pexels-photo-5886041.jpeg', // Veste homme
            'https://images.pexels.com/photos/7940627/pexels-photo-7940627.jpeg', // Sweat femme
            'https://images.pexels.com/photos/6311390/pexels-photo-6311390.jpeg', // Manteau noir homme
            'https://images.pexels.com/photos/10404233/pexels-photo-10404233.jpeg', // Robe beige
            'https://images.pexels.com/photos/9777364/pexels-photo-9777364.jpeg', // T-shirt blanc homme
            'https://images.pexels.com/photos/5886040/pexels-photo-5886040.jpeg', // Chemise bleue
            'https://images.pexels.com/photos/7940630/pexels-photo-7940630.jpeg', // Robe rouge
            'https://images.pexels.com/photos/6311384/pexels-photo-6311384.jpeg', // Sweat bleu
            'https://images.pexels.com/photos/10026494/pexels-photo-10026494.jpeg', // Veste verte femme

            // CHAUSSURES
            'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=400&h=400&fit=crop', // Sneakers blanches
            'https://images.unsplash.com/photo-1528701800489-20be7f14c5f2?w=400&h=400&fit=crop', // Talons noirs
            'https://images.unsplash.com/photo-1585386959984-a4155223f07b?w=400&h=400&fit=crop', // Chaussures de ville homme
            'https://images.unsplash.com/photo-1600185365925-3f019c5a2044?w=400&h=400&fit=crop', // Baskets femme
            'https://images.unsplash.com/photo-1595950653106-6b9df03f1f15?w=400&h=400&fit=crop', // Chaussures cuir homme
            'https://images.unsplash.com/photo-1618354691211-1867b6c3b22b?w=400&h=400&fit=crop', // Sneakers jaunes
            'https://images.unsplash.com/photo-1620207418302-439b387441b0?w=400&h=400&fit=crop', // Sandales été femme
        ];



        $thumbnail = $this->faker->randomElement($images);
        $image = $this->faker->randomElement($images);
        $hoverImage = $this->faker->randomElement($images);

        return [
            'id' => Str::uuid(),
            'matricule' => 'PROD-' . strtoupper(Str::random(6)),
            'title' => $title,
            'name' => $name,
            'description' => $this->faker->realTextBetween(100, 200),
            'price' => $price,
            'original_price' => $originalPrice,
            'discount_percentage' => $discountPercentage ?: 0,
            'discount' => $discount,
            'rating' => $this->faker->randomFloat(1, 3, 5),
            'stock' => $this->faker->numberBetween(0, 100),
            'brand' => $brand,
            'gender' => $gender,
            'thumbnail' => $thumbnail,
            'image' => $image,
            'hover_image' => $hoverImage,
            'is_new' => $this->faker->boolean(20),
            'is_best_seller' => $this->faker->boolean(10),
            'in_stock' => $this->faker->boolean(90),
            'is_on_sale' => $discountPercentage ? true : false,
            'promotion' => $this->faker->optional(0.3)->randomElement([
                "Offre spéciale : -{$discountPercentage}%",
                'Livraison gratuite',
                'Derniers jours !',
                'Promo exclusive membres',
                'Offre de lancement',
                'Édition limitée',
            ]),
            'category_id' => Category::factory(),
        ];
    }
}
