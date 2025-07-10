<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CategoryChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Répartition par Catégorie';
    protected static ?string $description = 'Nombre de produits par catégorie';
    protected static ?string $icon = 'heroicon-o-chart-pie';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $categoryData = Product::with('category')
            ->select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->having('total', '>', 0)
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category->name ?? 'Sans catégorie',
                    'total' => $item->total,
                ];
            });

        return [
            'datasets' => [
                [
                    'label' => 'Produits par catégorie',
                    'data' => $categoryData->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#3B82F6', // Blue
                        '#10B981', // Green
                        '#F59E0B', // Yellow
                        '#EF4444', // Red
                        '#8B5CF6', // Purple
                        '#06B6D4', // Cyan
                        '#F97316', // Orange
                        '#84CC16', // Lime
                    ],
                ],
            ],
            'labels' => $categoryData->pluck('category')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const label = context.label || "";
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ": " + value + " produits (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
        ];
    }
}
