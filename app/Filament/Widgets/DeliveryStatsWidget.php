<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeliveryStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalDeliveries = Delivery::count();
        $deliveredCount = Delivery::where('statut', 'livre')->count();
        $delayedCount = Delivery::where('statut', 'retarde')->count();
        $inTransitCount = Delivery::where('statut', 'en_transit')->count();
        $preparationCount = Delivery::where('statut', 'en_preparation')->count();

        $deliveryRate = $totalDeliveries > 0 ? round(($deliveredCount / $totalDeliveries) * 100, 1) : 0;

        return [
            Stat::make('Total Livraisons', $totalDeliveries)
                ->description('Toutes les livraisons')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            Stat::make('Livrées', $deliveredCount)
                ->description("{$deliveryRate}% de réussite")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('En transit', $inTransitCount)
                ->description('Livraisons en cours')
                ->descriptionIcon('heroicon-m-arrow-right-circle')
                ->color('info'),

            Stat::make('En préparation', $preparationCount)
                ->description('À expédier')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Retardées', $delayedCount)
                ->description('Nécessitent attention')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
