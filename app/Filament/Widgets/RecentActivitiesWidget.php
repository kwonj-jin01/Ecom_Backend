<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivitiesWidget extends BaseWidget
{
    protected static ?string $heading = 'Commandes Récentes';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest())
            ->columns([
                Tables\Columns\IconColumn::make('status')
                    ->label('')
                    ->icon(fn(string $state): string => match ($state) {
                        'payée', 'confirme' => 'heroicon-o-check-circle',
                        'livrée' => 'heroicon-o-truck',
                        'annulée' => 'heroicon-o-x-circle',
                        'en_attente' => 'heroicon-o-clock',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'payée', 'confirme' => 'success',
                        'livrée' => 'info',
                        'annulée' => 'danger',
                        'en_attente' => 'warning',
                        default => 'gray',
                    })
                    ->size('lg')
                    ->tooltip(fn(string $state): string => match ($state) {
                        'payée', 'confirme' => 'Commande payée',
                        'livrée' => 'Commande livrée',
                        'annulée' => 'Commande annulée',
                        'en_attente' => 'En attente de paiement',
                        default => 'Statut inconnu',
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->sortable()
                    ->weight('medium')
                    ->icon('heroicon-o-user')
                    ->copyable()
                    ->tooltip('Cliquez pour copier'),

                Tables\Columns\TextColumn::make('total')
                    ->label('Montant')
                    ->money('XOF', locale: 'fr_FR')
                    ->weight('bold')
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar')
                    ->alignment('right'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de commande')
                    ->dateTime('d M Y à H:i')
                    ->sortable()
                    ->color('gray')
                    ->icon('heroicon-o-calendar-days')
                    ->since()
                    ->tooltip(fn($record) => $record->created_at->format('l d F Y à H:i:s')),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => ['confirme', 'payée'],
                        'info' => 'livrée',
                        'danger' => 'annulée',
                        'warning' => 'en_attente',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'confirme' => 'Payée',
                        'payée' => 'Payée',
                        'livrée' => 'Livrée',
                        'annulée' => 'Annulée',
                        'en_attente' => 'En attente',
                        default => ucfirst($state),
                    })
                    ->icons([
                        'heroicon-o-check-circle' => ['confirme', 'payée'],
                        'heroicon-o-truck' => 'livrée',
                        'heroicon-o-x-circle' => 'annulée',
                        'heroicon-o-clock' => 'en_attente',
                    ])
                    ->size('sm'),
            ])
            ->striped()
            ->paginated(5) // Active la pagination avec 5 éléments par page
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10, 15]) // Options de pagination
            ->defaultSort('created_at', 'desc')
            ->poll('30s') // Actualise automatiquement toutes les 30 secondes
            ->emptyStateHeading('Aucune commande récente')
            ->emptyStateDescription('Les commandes apparaîtront ici dès qu\'elles seront créées.')
            ->emptyStateIcon('heroicon-o-shopping-cart');
    }
}
