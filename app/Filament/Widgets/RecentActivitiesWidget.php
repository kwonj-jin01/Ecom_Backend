<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\LoanActivity;

class RecentActivitiesWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Activities';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(LoanActivity::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn(string $state): string => match ($state) {
                        'disbursed' => 'heroicon-o-arrow-up-circle',
                        'received' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-clock',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'disbursed' => 'danger',
                        'received' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('day')
                    ->label('Day'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer Name'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('NGN'),

                Tables\Columns\TextColumn::make('loan_type')
                    ->label('Loan Type'),

                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->dateTime(),

                Tables\Columns\TextColumn::make('time')
                    ->label('Time'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'disbursed',
                        'success' => 'received',
                        'warning' => 'pending',
                    ]),
            ])
            ->paginated(false);
    }
}
