<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DeliveryChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Évolution des livraisons';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Delivery::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN statut = "livre" THEN 1 ELSE 0 END) as delivered'),
            DB::raw('SUM(CASE WHEN statut = "retarde" THEN 1 ELSE 0 END) as delayed')
        )
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => $data->pluck('total')->toArray(),
                    'borderColor' => 'rgb(99, 102, 241)',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                ],
                [
                    'label' => 'Livrées',
                    'data' => $data->pluck('delivered')->toArray(),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                ],
                [
                    'label' => 'Retardées',
                    'data' => $data->pluck('delayed')->toArray(),
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
