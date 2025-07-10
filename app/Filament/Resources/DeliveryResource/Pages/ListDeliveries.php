<?php

namespace App\Filament\Resources\DeliveryResource\Pages;

use App\Filament\Resources\DeliveryResource;
use App\Filament\Resources\DeliveryResource\Widgets\DeliveryStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveries extends ListRecords
{
    protected static string $resource = DeliveryResource::class;

   

    protected function getHeaderWidgets(): array
    {
        return [
            DeliveryStatsWidget::class,
        ];
    }
}
