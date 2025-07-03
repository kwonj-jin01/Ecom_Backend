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
            <div class="dark:bg-gray-800 bg-white rounded-xl shadow-md p-6 transition duration-300 hover:shadow-lg">
                <h3 class="text-lg font-semibold mb-6 text-gray-800 dark:text-gray-200">Active Users Ratio</h3>

                <!-- Donut Chart -->
                <div class="flex items-center justify-center">
                    <div class="relative w-32 h-32 animate-spin-slow">
                        <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                            <!-- Background Circle -->
                            <path d="M18 2.0845
                             a 15.9155 15.9155 0 0 1 0 31.831
                             a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#E5E7EB" stroke-width="3" />
                            <!-- Foreground Circle -->
                            <path d="M18 2.0845
                             a 15.9155 15.9155 0 0 1 0 31.831
                             a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#3B82F6" stroke-width="3"
                                stroke-dasharray="75, 100" stroke-linecap="round"
                                class="transition-all duration-700 ease-out" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-2xl font-bold text-blue-600">75%</span>
                        </div>
                    </div>
                </div>

                <!-- Labels -->
                <div class="mt-6 space-y-3 text-sm">
                    <div
                        class="flex items-center justify-between hover:bg-gray-100 px-2 py-1 rounded transition duration-200">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-gray-600 dark:text-gray-300">Male</span>
                        </div>
                        <span class="font-medium text-gray-700 dark:text-gray-200">6,472</span>
                    </div>
                    <div
                        class="flex items-center justify-between hover:bg-gray-100 px-2 py-1 rounded transition duration-200">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
                            <span class="text-gray-600 dark:text-gray-300">Female</span>
                        </div>
                        <span class="font-medium text-gray-700 dark:text-gray-200">4,314</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Activities -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        @livewire(\App\Filament\Widgets\RecentActivitiesWidget::class)
    </div>
</x-filament-panels::page>
