<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoanChartWidget extends ChartWidget
{
    protected static string $color = 'info';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = collect(range(0, 11))->map(function ($i) {
            return Carbon::now()->subMonths(11 - $i)->format('Y-m');
        });

        // Revenus (commandes payées)
        $sales = DB::table('orders')
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, SUM(total) as total")
            ->where('status', 'payée')
            ->whereBetween('created_at', [now()->subYear(), now()])
            ->groupBy('month')
            ->pluck('total', 'month');

        $delivered = DB::table('orders')
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, COUNT(*) as count")
            ->where('status', 'livrée')
            ->whereBetween('created_at', [now()->subYear(), now()])
            ->groupBy('month')
            ->pluck('count', 'month');

        $cancelled = DB::table('orders')
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, COUNT(*) as count")
            ->where('status', 'annulée')
            ->whereBetween('created_at', [now()->subYear(), now()])
            ->groupBy('month')
            ->pluck('count', 'month');


        $salesData = [];
        $deliveredData = [];
        $cancelledData = [];
        $labels = [];

        foreach ($months as $month) {
            $labels[] = Carbon::createFromFormat('Y-m', $month)->format('M');
            $salesData[] = $sales[$month] ?? 0;
            $deliveredData[] = $delivered[$month] ?? 0;
            $cancelledData[] = $cancelled[$month] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Chiffre d’Affaires',
                    'data' => $salesData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',   // Vert
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Commandes Livrées',
                    'data' => $deliveredData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',  // Bleu
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Commandes Annulées',
                    'data' => $cancelledData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',   // Rouge
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true, // ✅ Forcer l'axe Y à commencer à 0
                ],
            ],
        ];
    }
}
