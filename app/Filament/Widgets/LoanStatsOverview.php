<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
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
            Stat::make('Total Ventes', number_format($totalVentes, 0, ',', ' ') . ' F CFA')
                ->description('Chiffre d’affaires')
                ->descriptionIcon('heroicon-o-currency-euro')
                ->color($totalVentes > 0 ? 'success' : 'danger')
                ->chart([5, 9, 7, 6, 10, 12, 14])
                ->extraAttributes([
                    'class' => $totalVentes > 0
                        ? 'bg-green-100 text-green-900 animate-fade-in transition duration-500 ease-out'
                        : 'bg-red-100 text-red-900 animate-shake transition duration-500 ease-in',
                ]),


            Stat::make('Total Commandes', $totalCommandes)
                ->description('Toutes les commandes reçues')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary')
                ->chart([5, 9, 7, 6, 10, 12, 14])
                ->extraAttributes([
                    'class' => 'hover:scale-105 transform transition duration-300 ease-in-out',
                ]),

            Stat::make('Commandes en attente', $commandesEnAttente)
                ->description('À traiter')
                ->descriptionIcon('heroicon-o-clock')
                ->color($commandesEnAttente > 5 ? 'warning' : 'success')
                ->chart([5, 9, 7, 6, 10, 12, 14])
                ->extraAttributes([
                    'class' => $commandesEnAttente > 5
                        ? 'bg-yellow-100 text-yellow-800 animate-pulse'
                        : 'bg-green-100 text-green-800',
                ]),

            Stat::make('Produits en rupture', $produitsRupture)
                ->description('Stock à réapprovisionner')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($produitsRupture > 0 ? 'danger' : 'info')
                ->chart([5, 9, 7, 6, 10, 12, 14])
                ->extraAttributes([
                    'class' => $produitsRupture > 0
                        ? 'bg-red-100 text-red-900 animate-bounce'
                        : 'bg-green-100 text-green-800',
                ]),

            Stat::make('Commandes Livrées', $commandesLivrees)
                ->description('Commandes livrées avec succès')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning')
                ->chart([5, 9, 7, 6, 10, 12, 14])
                ->extraAttributes([
                    'class' => 'bg-blue-100 text-blue-800 hover:bg-blue-200 transition duration-300 ease-in-out',
                ]),
        ];
    }


    protected static ?int $sort = 1;
}
