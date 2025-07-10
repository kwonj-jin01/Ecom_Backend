<?php

namespace App\Filament\Resources\CategoryResource\Widgets;

use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CategoryDetailWidget extends BaseWidget
{
    public ?Category $record = null;

    protected function getStats(): array
    {
        if (!$this->record) {
            return [];
        }

        $totalProducts = $this->record->products()->count();
        $activeProducts = $this->record->products()->where('is_active', true)->count();
        $inactiveProducts = $this->record->products()->where('is_active', false)->count();
        $averagePrice = $this->record->products()->avg('price') ?? 0;

        return [
            Stat::make('Produits total', $totalProducts)
                ->description('Dans cette catégorie')
                ->descriptionIcon('heroicon-o-cube')
                ->color('primary')
                ->chart([1, 3, 5, 7, $totalProducts]),

            Stat::make('Produits actifs', $activeProducts)
                ->description('Disponibles à la vente')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([1, 2, 3, $activeProducts]),

            Stat::make('Produits inactifs', $inactiveProducts)
                ->description('Non disponibles')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color($inactiveProducts > 0 ? 'warning' : 'success')
                ->chart([$inactiveProducts, 2, 1, 0]),

            Stat::make('Prix moyen', number_format($averagePrice, 2) . ' €')
                ->description('Prix moyen des produits')
                ->descriptionIcon('heroicon-o-currency-euro')
                ->color('info')
                ->chart([50, 75, 100, $averagePrice]),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
