<?php

namespace App\Filament\Resources\CategoryResource\Widgets;

use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CategoryStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCategories = Category::count();

        $categoriesWithProducts = Category::has('products')->count();

        $categoriesWithoutProducts = Category::doesntHave('products')->count();

        $avgProductsPerCategory = Category::withCount('products')->get()->avg('products_count');

        return [
            Stat::make('Total des catégories', $totalCategories)
                ->description('Nombre total de catégories')
                ->descriptionIcon('heroicon-o-tag')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Catégories avec produits', $categoriesWithProducts)
                ->description('Catégories ayant au moins un produit')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([2, 4, 6, 8, 10, 12, 14]),

            Stat::make('Catégories sans produits', $categoriesWithoutProducts)
                ->description('Catégories vides')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($categoriesWithoutProducts > 0 ? 'warning' : 'success')
                ->chart([10, 8, 6, 4, 2, 1, 0]),

            Stat::make('Moyenne produits/catégorie', number_format($avgProductsPerCategory, 1))
                ->description('Nombre moyen de produits par catégorie')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('info')
                ->chart([5, 7, 8, 6, 9, 10, 8]),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
