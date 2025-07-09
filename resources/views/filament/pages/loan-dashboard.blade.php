{{-- resources/views/filament/pages/loan-dashboard.blade.php --}}
<x-filament-panels::page>


    <!-- Stats Cards -->
    @livewire(\App\Filament\Widgets\LoanStatsOverview::class)


    <!-- Chart et Stats utilisateurs -->
    <div class="chart-container">

        <div class="chart-card">
            @livewire(\App\Filament\Widgets\LoanChartWidget::class)
        </div>

        <div class=" sidebar-cards">
            @livewire(\App\Filament\Widgets\UserStatsCustom::class)
        </div>

        <!-- Graphique circulaire pour les utilisateurs actifs -->
        <div class="sidebar-cards-right">
            @livewire(\App\Filament\Widgets\ActivityProfitWidget::class)
        </div>

    </div>

    <!-- Recent Activities -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        @livewire(\App\Filament\Widgets\RecentActivitiesWidget::class)
    </div>
</x-filament-panels::page>

