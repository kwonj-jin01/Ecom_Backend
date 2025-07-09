<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Order;
use Filament\Widgets\Widget;

class UserStatsCustom extends Widget
{
    protected static string $view = 'filament.widgets.user-stats-custom';

    protected int | string | array $columnSpan = 2;

    protected static ?string $heading = 'Statistiques Utilisateurs';

    public function getStats(): array
    {
        $totalUsers = User::count();
        $usersWithOrders = User::whereHas('orders')->count();
        $orderRate = $totalUsers > 0
            ? round(($usersWithOrders / $totalUsers) * 100) . '%'
            : '0%';

        return [
            [
                'title' => 'Utilisateurs Totaux',
                'value' => number_format($totalUsers, 0, ',', ' '),
                'color' => 'bg-blue-600',
            ],
            [
                'title' => 'Clients Ayant Commandé',
                'value' => number_format($usersWithOrders, 0, ',', ' '),
                'color' => 'bg-green-600',
            ],
            [
                'title' => 'Taux de Clients Actifs',
                'value' => $orderRate,
                'color' => 'bg-yellow-500',
            ],
        ];
    }
}
