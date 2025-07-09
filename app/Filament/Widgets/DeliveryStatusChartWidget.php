<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use Filament\Widgets\ChartWidget;

class DeliveryStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Répartition par statut';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $statusCounts = Delivery::selectRaw('statut, COUNT(*) as count')
            ->groupBy('statut')
            ->pluck('count', 'statut')
            ->toArray();

        $labels = [
            'en_preparation' => 'En préparation',
            'en_transit' => 'En transit',
            'livre' => 'Livré',
            'retarde' => 'Retardé'
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($statusCounts),
                    'backgroundColor' => [
                        'rgba(251, 191, 36, 0.8)',  // warning
                        'rgba(59, 130, 246, 0.8)',  // info
                        'rgba(34, 197, 94, 0.8)',   // success
                        'rgba(239, 68, 68, 0.8)',   // danger
                    ],
                ]
            ],
            'labels' => array_map(fn($status) => $labels[$status] ?? $status, array_keys($statusCounts)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
