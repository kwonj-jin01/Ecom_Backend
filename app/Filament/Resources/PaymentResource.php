<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationGroup = 'E-commerce';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\BelongsToSelect::make('commande')
                ->relationship('commande', 'id')->required(),
            Forms\Components\TextInput::make('montant')->numeric()->required(),
            Forms\Components\Select::make('méthode')
                ->options([
                    'carte' => 'Carte',
                    'mobile_money' => 'Mobile Money',
                    'paypal' => 'PayPal',
                    'espèce' => 'Espèce',
                ])->required(),
            Forms\Components\Select::make('statut')
                ->options([
                    'en_attente' => 'En attente',
                    'effectué' => 'Effectué',
                    'échoué' => 'Échoué',
                ])->required(),
            Forms\Components\DateTimePicker::make('date_paiement')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('commande.id')->label('Commande'),
            Tables\Columns\TextColumn::make('montant')->money('XOF'),
            Tables\Columns\TextColumn::make('méthode'),
            Tables\Columns\BadgeColumn::make('statut')
                ->colors([
                    'warning' => 'en_attente',
                    'success' => 'effectué',
                    'danger' => 'échoué',
                ]),
            Tables\Columns\TextColumn::make('date_paiement')->dateTime(),
        ]);
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
