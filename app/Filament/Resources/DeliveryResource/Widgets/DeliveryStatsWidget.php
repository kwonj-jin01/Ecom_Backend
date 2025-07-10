<?php

namespace App\Filament\Resources\DeliveryResource\Widgets;

use App\Models\Delivery;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeliveryStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalDeliveries = Delivery::count();
        $deliveredCount = Delivery::where('statut', 'livre')->count();
        $pendingCount = Delivery::whereIn('statut', ['en_preparation', 'en_transit'])->count();
        $delayedCount = Delivery::where('statut', 'retarde')->count();

        // Calcul du pourcentage de livraisons réussies
        $successRate = $totalDeliveries > 0 ? round(($deliveredCount / $totalDeliveries) * 100, 1) : 0;

        return [
            Stat::make('Total des livraisons', $totalDeliveries)
                ->description('Nombre total de livraisons')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->chartColor('blue'),

            Stat::make('Livraisons effectuées', $deliveredCount)
                ->description("Taux de réussite: {$successRate}%")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([3, 5, 2, 8, 12, 6, 15])
                ->chartColor('green'),

            Stat::make('Livraisons en attente', $pendingCount)
                ->description('En préparation ou en transit')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([2, 4, 6, 3, 8, 5, 10])
                ->chartColor('orange'),

            Stat::make('Livraisons retardées', $delayedCount)
                ->description($delayedCount > 0 ? 'Attention requise' : 'Aucun retard')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($delayedCount > 0 ? 'danger' : 'success')
                ->chart([1, 0, 2, 1, 3, 0, 1])
                ->chartColor($delayedCount > 0 ? 'red' : 'green'),
        ];
    }
}
