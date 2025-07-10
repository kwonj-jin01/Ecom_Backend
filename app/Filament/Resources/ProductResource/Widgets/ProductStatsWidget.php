<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ProductStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProducts = Product::count();
        $productsInStock = Product::where('in_stock', true)->count();
        $outOfStock = Product::where('stock', '<=', 0)->count();
        $lowStock = Product::where('stock', '>', 0)->where('stock', '<', 5)->count();
        $newProducts = Product::where('is_new', true)->count();
        $bestSellers = Product::where('is_best_seller', true)->count();
        $onSale = Product::where('is_on_sale', true)->count();
        $averagePrice = Product::avg('price');
        $totalStockValue = Product::sum(DB::raw('stock * price'));

        return [
            Stat::make('Total Produits', $totalProducts)
                ->description('Nombre total de produits')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('En Stock', $productsInStock)
                ->description('Produits disponibles')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([3, 5, 6, 7, 8, 5, 6, 7])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Rupture de Stock', $outOfStock)
                ->description('Produits indisponibles')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger')
                ->chart([1, 2, 1, 3, 2, 1, 2, 1])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Stock Faible', $lowStock)
                ->description('Stock < 5 unités')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->chart([2, 3, 2, 4, 3, 2, 3, 2])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Nouveaux Produits', $newProducts)
                ->description('Marqués comme nouveaux')
                ->descriptionIcon('heroicon-o-star')
                ->color('info')
                ->chart([1, 2, 3, 2, 4, 3, 2, 3])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Best Sellers', $bestSellers)
                ->description('Meilleures ventes')
                ->descriptionIcon('heroicon-o-trophy')
                ->color('warning')
                ->chart([2, 4, 3, 5, 4, 3, 4, 5])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('En Promotion', $onSale)
                ->description('Produits en promo')
                ->descriptionIcon('heroicon-o-tag')
                ->color('danger')
                ->chart([1, 3, 2, 4, 3, 2, 3, 4])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Prix Moyen', '€' . number_format($averagePrice, 2))
                ->description('Prix moyen des produits')
                ->descriptionIcon('heroicon-o-currency-euro')
                ->color('success')
                ->chart([25, 30, 28, 35, 32, 30, 33, 35])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Valeur Stock', '€' . number_format($totalStockValue, 2))
                ->description('Valeur totale du stock')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary')
                ->chart([100, 120, 110, 130, 125, 115, 135, 140])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
