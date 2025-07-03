<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'E-commerce';
    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
    protected static ?string $navigationLabel = 'Commandes';
    protected static ?string $pluralModelLabel = 'Commandes';
    protected static ?string $modelLabel = 'Commande';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('date_commande')
                    ->label('Date de commande')
                    ->required()
                    ->default(now()),

                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->required()
                    ->options([
                        'en_attente' => 'En attente',
                        'payée' => 'Payée',
                        'expédiée' => 'Expédiée',
                        'livrée' => 'Livrée',
                        'annulée' => 'Annulée',
                    ])
                    ->default('en_attente'),
                Forms\Components\Select::make('utilisateur_id')
                    ->label('Client')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('N°'),
                Tables\Columns\TextColumn::make('user.name')->label('Client')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('date_commande')->label('Date')->dateTime(),
                BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'secondary' => 'en_attente',
                        'success' => 'payée',
                        'warning' => 'expédiée',
                        'info' => 'livrée',
                        'danger' => 'annulée',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('XOF')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'en_attente' => 'En attente',
                        'payée' => 'Payée',
                        'expédiée' => 'Expédiée',
                        'livrée' => 'Livrée',
                        'annulée' => 'Annulée',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('date_commande', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
