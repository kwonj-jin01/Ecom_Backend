<x-filament-widgets::widget>
    <div class="flex flex-col items-center space-y-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Rentabilité de l’activité</h3>

        <!-- Donut Chart -->
        <div class="relative w-32 h-32">
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                <!-- Background circle -->
                <path d="M18 2.0845
                    a 15.9155 15.9155 0 0 1 0 31.831
                    a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#E5E7EB" stroke-width="3" />

                <!-- Foreground arc -->
                <path d="M18 2.0845
                    a 15.9155 15.9155 0 0 1 0 31.831
                    a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10B981" {{-- green-500 --}}
                    stroke-width="3" stroke-dasharray="{{ $rentabilite }}, 100" stroke-linecap="round"
                    class="transition-all duration-700 ease-out" />
            </svg>

            <!-- Pourcentage -->
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-xl font-bold text-green-600">{{ $rentabilite }}%</span>
            </div>
        </div>

        <!-- Détails -->
        <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1 mt-2">
            <div><strong>Revenu :</strong> {{ number_format($revenu, 0, ',', ' ') }} F CFA</div>
            <div><strong>Dépenses :</strong> {{ number_format($depenses, 0, ',', ' ') }} F CFA</div>
            <div><strong>Bénéfice :</strong> {{ number_format($profit, 0, ',', ' ') }} F CFA</div>
        </div>
    </div>
</x-filament-widgets::widget>
