<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class UserStatsCustom extends Widget
{
    protected static string $view = 'filament.widgets.user-stats-custom';

    protected int | string | array $columnSpan = 2;

    protected static ?string $heading = 'Statistiques Utilisateurs';

    public function getStats(): array
    {
        return [
            [
                'title' => 'Utilisateurs Actifs',
                'value' => '10,786',
                'color' => 'bg-green-500',
            ],
            [
                'title' => 'Utilisateurs Totaux',
                'value' => '20,587',
                'color' => 'bg-blue-500',
            ],
            [
                'title' => 'Taux de Remboursement',
                'value' => '80%',
                'color' => 'bg-yellow-500',
            ],
        ];
    }
}
