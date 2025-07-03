<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryResource\Pages;
use App\Filament\Resources\DeliveryResource\RelationManagers;
use App\Models\Delivery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;
    protected static ?string $navigationGroup = 'E-commerce';
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\BelongsToSelect::make('commande')
                ->relationship('commande', 'id')->required(),
            Forms\Components\TextInput::make('adresse_livraison')->required(),
            Forms\Components\TextInput::make('transporteur'),
            Forms\Components\Select::make('statut')
                ->options([
                    'en préparation' => 'En préparation',
                    'en transit' => 'En transit',
                    'livré' => 'Livré',
                    'retardé' => 'Retardé',
                ])->required(),
            Forms\Components\DatePicker::make('date_estimee')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('commande.id')->label('Commande'),
            Tables\Columns\TextColumn::make('adresse_livraison')->limit(20),
            Tables\Columns\TextColumn::make('transporteur'),
            Tables\Columns\BadgeColumn::make('statut')
                ->colors([
                    'gray' => 'en préparation',
                    'warning' => 'en transit',
                    'success' => 'livré',
                    'danger' => 'retardé',
                ]),
            Tables\Columns\TextColumn::make('date_estimee')->date(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveries::route('/'),
            'create' => Pages\CreateDelivery::route('/create'),
            'edit' => Pages\EditDelivery::route('/{record}/edit'),
        ];
    }
}
