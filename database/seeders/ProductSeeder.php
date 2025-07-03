<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\ProductColor;
use App\Models\ProductDetail;
use App\Models\About;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            // Créer 5-10 produits par catégorie
            $products = Product::factory()
                ->count(rand(5, 10))
                ->create(['category_id' => $category->id]);

            foreach ($products as $product) {
                // Créer des images pour chaque produit
                ProductImage::factory()
                    ->count(rand(2, 5))
                    ->create(['product_id' => $product->id]);

                // Créer des tailles pour chaque produit
                $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                $selectedSizes = array_rand(array_flip($sizes), rand(2, 4));
                foreach ((array)$selectedSizes as $size) {
                    ProductSize::factory()->create([
                        'product_id' => $product->id,
                        'size' => $size
                    ]);
                }

                // Créer des couleurs pour chaque produit
                $colors = ['Rouge', 'Bleu', 'Vert', 'Noir', 'Blanc', 'Gris', 'Jaune', 'Rose'];
                $selectedColors = array_rand(array_flip($colors), rand(2, 4));
                foreach ((array)$selectedColors as $color) {
                    ProductColor::factory()->create([
                        'product_id' => $product->id,
                        'name' => $color
                    ]);
                }

                // Créer des détails pour chaque produit
                $details = [
                    ['label' => 'Matière', 'value' => 'Coton 100%'],
                    ['label' => 'Origine', 'value' => 'Fabriqué en France'],
                    ['label' => 'Entretien', 'value' => 'Lavage à 30°C'],
                    ['label' => 'Composition', 'value' => 'Matière principale: Coton'],
                ];
                foreach ($details as $detail) {
                    ProductDetail::factory()->create([
                        'product_id' => $product->id,
                        'label' => $detail['label'],
                        'value' => $detail['value']
                    ]);
                }

                // Créer une description "About" pour chaque produit
                About::factory()->create(['product_id' => $product->id]);
            }
        }
    }
}
