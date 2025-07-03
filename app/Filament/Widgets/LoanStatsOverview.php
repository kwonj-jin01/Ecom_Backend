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
        return [
             Stat::make('Total Ventes', Order::where('status', 'payée')->sum('total'))
                ->description('Chiffre d’affaires')
                ->descriptionIcon('heroicon-o-currency-euro')
                ->color('success'),

            Stat::make('Commandes en attente', Order::where('status', 'en_attente')->count())
                ->description('À traiter')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Clients', User::where('role', 'client')->count())
                ->description('Comptes clients')
                ->descriptionIcon('heroicon-o-user-group'),

            Stat::make('Produits en rupture', Product::where('stock', '<', 1)->count())
                ->description('Stock à réapprovisionner')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make('Total Commandes', 1)
                ->description('Toutes les commandes reçues')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary')
                ->chart([5, 9, 7, 6, 10, 12, 14]),

            Stat::make('Commandes Livrées', 1000)
                ->description('Commandes livrées avec succès')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),

            Stat::make('Revenu Total', '€' . number_format(1, 2))
                ->description('Montant total encaissé')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('info'),

            Stat::make('Taux d’Annulation', $this->getCancelRate() . '%')
                ->description('Commandes annulées vs total')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($this->getCancelRate() > 5 ? 'danger' : 'success'),

            Stat::make('Clients', User::where('role', 'customer')->count())
                ->description('Nombre de clients enregistrés')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
        ];
    }

    private function getCancelRate(): float
    {
        $totalOrders = Order::count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        return $totalOrders > 0 ? round(($cancelledOrders / $totalOrders) * 100, 1) : 0;
    }

    protected static ?int $sort = 1;
}
