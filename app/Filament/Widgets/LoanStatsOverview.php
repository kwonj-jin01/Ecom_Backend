<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LoanStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalVentes = Order::where('status', 'payée')->sum('total');
        $commandesEnAttente = Order::where('status', 'en_attente')->count();
        $produitsRupture = Product::where('stock', '<', 1)->count();
        $totalCommandes = Order::count();
        $commandesLivrees = Order::where('status', 'livrée')->count();

        return [
            Stat::make('Total Ventes', view('components.animated-counter', [
                'value' => 10000,
                'index' => 0,
            ]))
                ->description('Chiffre d’affaires')
                ->descriptionIcon('heroicon-o-currency-euro')
                ->color($totalVentes > 0 ? 'success' : 'danger')
                ->chart([5, 9, 7, 6, 10, 12, 14])
                ->extraAttributes([
                    'class' => 'animated-stats-card ' . ($totalVentes > 0 ? 'bg-success' : 'bg-danger'),
                    'data-index' => 0,
                ]),

            Stat::make('Total Commandes', view('components.animated-counter', [
                'value' => 1000,
                'index' => 1,
            ]))
                ->description('Commandes reçues')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary')
                ->chart([5, 9, 7, 6, 10, 12, 14])
                ->extraAttributes([
                    'class' => 'animated-stats-card bg-primary',
                    'data-index' => 1,
                ]),

            Stat::make('Commandes en attente', view('components.animated-counter', [
                'value' => 1999,
                'index' => 2,
            ]))
                ->description('À traiter')
                ->descriptionIcon('heroicon-o-clock')
                ->color($commandesEnAttente > 5 ? 'warning' : 'success')
                ->chart([5, 9, 7, 6, 10, 12, 14])
                ->extraAttributes([
                    'class' => 'animated-stats-card ' . ($commandesEnAttente > 5 ? 'bg-warning' : 'bg-success'),
                    'data-index' => 2,
                ]),

            Stat::make('Produits en rupture', view('components.animated-counter', [
                'value' => 99,
                'index' => 3,
            ]))
                ->description('Stock à réapprovisionner')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($produitsRupture > 0 ? 'danger' : 'info')
                ->chart([5, 9, 7, 6, 10, 12, 14])
                ->extraAttributes([
                    'class' => 'animated-stats-card ' . ($produitsRupture > 0 ? 'bg-danger' : 'bg-info'),
                    'data-index' => 3,
                ]),

            Stat::make('Commandes Livrées', view('components.animated-counter', [
                'value' => 55,
                'index' => 4,
            ]))
                ->description('Commandes livrées avec succès')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning')
                ->chart([5, 9, 7, 6, 10, 12, 14])
                ->extraAttributes([
                    'class' => 'animated-stats-card bg-warning',
                    'data-index' => 4,
                ]),
        ];
    }

    protected static ?int $sort = 1;
}
