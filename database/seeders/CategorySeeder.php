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
            ['name' => 'Vêtements Homme' ],
            ['name' => 'Vêtements Femme'],
            ['name' => 'Chaussures'],
            ['name' => 'Accessoires'],
            ['name' => 'Bijoux'],
            ['name' => 'Sacs'],
            ['name' => 'Montres'],
            ['name' => 'Parfums'],
        ];

        foreach ($categories as $category) {
            Category::factory()->create($category);
        }
    }
}
