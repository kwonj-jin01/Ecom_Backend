<x-filament-widgets::widget>
    <div class="flex flex-col gap-8 h-full">
        @foreach ($this->getStats() as $stat)
            <div class=" rounded-xl shadow p-4  {{ $stat['color'] }} text-black">
                <div class="text-sm font-medium">{{ $stat['title'] }}</div>
                <div class="text-2xl font-bold">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
