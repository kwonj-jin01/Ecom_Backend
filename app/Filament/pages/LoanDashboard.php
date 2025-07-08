<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ActivityProfitWidget;
use App\Filament\Widgets\LoanChartWidget;
use App\Filament\Widgets\LoanStatsOverview;
use App\Filament\Widgets\RecentActivitiesWidget;
use App\Filament\Widgets\UserStatsWidget;
use App\Filament\Widgets\UserStatsCustom;
use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Loan;
use App\Models\User;

class LoanDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $title = 'Loan Dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $slug = 'loan-dashboard'; // assure l'URL

    protected static string $view = 'filament.pages.loan-dashboard'; // Crée cette vue Blade si nécessaire

    public function getColumns(): int | string | array
    {

        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 5,
            'xl' => 5,
        ];
    }

    public function getWidgets(): array
    {
        return [
            LoanStatsOverview::class,
            LoanChartWidget::class,
            UserStatsCustom::class,
            ActivityProfitWidget::class,
            RecentActivitiesWidget::class,
        ];
    }
}
