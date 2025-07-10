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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;
    protected static ?string $navigationLabel = 'Livraisons';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de base')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->label('Commande')
                            ->relationship('order', 'id')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Textarea::make('adresse_livraison')
                            ->label('Adresse de livraison')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('transporteur')
                            ->label('Transporteur')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Statut et dates')
                    ->schema([
                        Forms\Components\Select::make('statut')
                            ->label('Statut')
                            ->options([
                                'en_preparation' => 'En préparation',
                                'en_transit' => 'En transit',
                                'livre' => 'Livré',
                                'retarde' => 'Retardé'
                            ])
                            ->default('en_preparation')
                            ->required(),

                        Forms\Components\DatePicker::make('date_estimee')
                            ->label('Date estimée')
                            ->native(false),

                        Forms\Components\DateTimePicker::make('date_livraison_reelle')
                            ->label('Date de livraison réelle')
                            ->native(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.id')
                    ->label('Commande')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('adresse_livraison')
                    ->label('Adresse')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('transporteur')
                    ->label('Transporteur')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('statut')
                    ->label('Statut')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'en_preparation' => 'En préparation',
                        'en_transit' => 'En transit',
                        'livre' => 'Livré',
                        'retarde' => 'Retardé'
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'en_preparation' => 'warning',
                        'en_transit' => 'info',
                        'livre' => 'success',
                        'retarde' => 'danger'
                    }),

                Tables\Columns\TextColumn::make('date_estimee')
                    ->label('Date estimée')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date_livraison_reelle')
                    ->label('Date réelle')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'en_preparation' => 'En préparation',
                        'en_transit' => 'En transit',
                        'livre' => 'Livré',
                        'retarde' => 'Retardé'
                    ]),

                SelectFilter::make('transporteur')
                    ->label('Transporteur')
                    ->options(fn() => Delivery::pluck('transporteur', 'transporteur')->toArray()),

                Filter::make('date_estimee')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Date estimée du'),
                        Forms\Components\DatePicker::make('until')
                            ->label('au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_estimee', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_estimee', '<=', $date),
                            );
                    }),

                Filter::make('retard')
                    ->label('Livraisons en retard')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('date_estimee', '<', now())
                            ->whereNull('date_livraison_reelle')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListDeliveries::route('/'),
            'create' => Pages\CreateDelivery::route('/create'),
            'edit' => Pages\EditDelivery::route('/{record}/edit'),
        ];
    }
}
