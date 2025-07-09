<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Order;
use App\Models\Expense;

class ActivityProfitWidget extends Widget
{
    protected static string $view = 'filament.widgets.activity-profit-widget';
    protected int|string|array $columnSpan = 'full';

    public float $revenu = 0;
    public float $depenses = 0;
    public float $profit = 0;
    public float $rentabilite = 0;

    public function mount(): void
    {
        // Calcul revenu = total commandes payées
        $this->revenu = Order::where('status', 'confirme')->sum('total');

        // Dépenses fictives ou depuis une table `expenses`
        $this->depenses = Expense::sum('amount'); // ou une valeur statique si pas encore implémentée

        // Calcul profit & rentabilité
        $this->profit = $this->revenu - $this->depenses;
        $this->rentabilite = $this->revenu > 0
            ? round(($this->profit / $this->revenu) * 100, 2)
            : 0;
    }
}
