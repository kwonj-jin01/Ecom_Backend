<?php

// database/factories/CategoryFactory.php
namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'matricule' => 'CAT-' . strtoupper(Str::random(6)),
            'name' => $this->faker->randomElement([
                'Vêtements',
                'Chaussures',
                'Accessoires',
                'Bijoux',
                'Sacs',
                'Montres',
                'Lunettes',
                'Parfums'
            ]),
        ];
    }
}
