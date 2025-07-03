<?php

// database/seeders/CategorySeeder.php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Vêtements Homme', 'image' => 'https://via.placeholder.com/400x300/007bff/ffffff?text=Vêtements+Homme'],
            ['name' => 'Vêtements Femme', 'image' => 'https://via.placeholder.com/400x300/dc3545/ffffff?text=Vêtements+Femme'],
            ['name' => 'Chaussures', 'image' => 'https://via.placeholder.com/400x300/28a745/ffffff?text=Chaussures'],
            ['name' => 'Accessoires', 'image' => 'https://via.placeholder.com/400x300/ffc107/ffffff?text=Accessoires'],
            ['name' => 'Bijoux', 'image' => 'https://via.placeholder.com/400x300/17a2b8/ffffff?text=Bijoux'],
            ['name' => 'Sacs', 'image' => 'https://via.placeholder.com/400x300/6f42c1/ffffff?text=Sacs'],
            ['name' => 'Montres', 'image' => 'https://via.placeholder.com/400x300/fd7e14/ffffff?text=Montres'],
            ['name' => 'Parfums', 'image' => 'https://via.placeholder.com/400x300/e83e8c/ffffff?text=Parfums'],
        ];

        foreach ($categories as $category) {
            Category::factory()->create($category);
        }
    }
}
