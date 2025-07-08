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
            ->query(Order::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn(string $state): string => match ($state) {
                        'payée' => 'heroicon-o-check-circle',
                        'livrée' => 'heroicon-o-truck',
                        'annulée' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-clock',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'payée' => 'success',
                        'livrée' => 'info',
                        'annulée' => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client'),

                Tables\Columns\TextColumn::make('total')
                    ->label('Montant')
                    ->money('XOF', locale: 'fr_FR'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Paiement'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y - H:i'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->color(fn(string $state): string => match ($state) {
                        'payée' => 'success',
                        'livrée' => 'info',
                        'annulée' => 'danger',
                        'en_attente' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'payée' => 'Payée',
                        'livrée' => 'Livrée',
                        'annulée' => 'Annulée',
                        'en_attente' => 'En attente',
                        default => ucfirst($state),
                    }),


            ])
            ->paginated(false);
    }
}
