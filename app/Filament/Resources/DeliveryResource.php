<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryResource\Pages;
use App\Filament\Resources\DeliveryResource\RelationManagers;
use App\Filament\Resources\DeliveryResource\Widgets\DeliveryStatsWidget;
use App\Models\Delivery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\StatsOverviewWidget\Card;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;
    protected static ?string $navigationLabel = 'Livraisons';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'id';

    // Configuration pour les UUID
    protected static ?string $recordRouteKeyName = 'id';

    // Forcer l'utilisation d'UUID pour la recherche
    protected static bool $shouldRegisterNavigation = true;

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
                    ])->columns(2),
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

                Tables\Columns\TextColumn::make('statut')
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
                            ->whereNotIn('statut', ['livre'])
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Voir')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->modalHeading('Détails de la livraison')
                    ->modalWidth('4xl')
                    ->infolist([
                        Section::make('📦 Détails de la livraison')
                            ->description('Informations principales de la livraison')
                            ->icon('heroicon-m-truck')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('id')
                                            ->label('ID Livraison')
                                            ->copyable()
                                            ->copyMessage('ID copié!')
                                            ->copyMessageDuration(1500)
                                            ->badge()
                                            ->color('gray'),

                                        TextEntry::make('order.id')
                                            ->label('Commande')
                                            ->badge()
                                            ->color('blue')
                                            ->url(fn($record) => $record->order ? route('filament.admin.resources.orders.view', $record->order) : null),

                                        TextEntry::make('statut')
                                            ->label('Statut')
                                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                                'en_preparation' => 'En préparation',
                                                'en_transit' => 'En transit',
                                                'livre' => 'Livré',
                                                'retarde' => 'Retardé'
                                            })
                                            ->badge()
                                            ->color(fn(string $state): string => match ($state) {
                                                'en_preparation' => 'warning',
                                                'en_transit' => 'info',
                                                'livre' => 'success',
                                                'retarde' => 'danger'
                                            })
                                            ->icon(fn(string $state): string => match ($state) {
                                                'en_preparation' => 'heroicon-m-clock',
                                                'en_transit' => 'heroicon-m-truck',
                                                'livre' => 'heroicon-m-check-circle',
                                                'retarde' => 'heroicon-m-exclamation-triangle'
                                            }),
                                    ]),

                                TextEntry::make('adresse_livraison')
                                    ->label('Adresse de livraison')
                                    ->columnSpanFull()
                                    ->icon('heroicon-m-map-pin')
                                    ->copyable()
                                    ->copyMessage('Adresse copiée!')
                                    ->placeholder('Aucune adresse spécifiée'),

                                TextEntry::make('transporteur')
                                    ->label('Transporteur')
                                    ->icon('heroicon-m-building-office-2')
                                    ->badge()
                                    ->color('indigo')
                                    ->placeholder('Non spécifié'),
                            ])
                            ->collapsible()
                            ->persistCollapsed()
                            ->columns(1),

                        Section::make('📅 Informations temporelles')
                            ->description('Dates et délais de livraison')
                            ->icon('heroicon-m-calendar')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('date_estimee')
                                            ->label('Date estimée')
                                            ->date('d/m/Y')
                                            ->icon('heroicon-m-calendar-days')
                                            ->color(function ($record) {
                                                if (!$record->date_estimee) return 'gray';
                                                $now = now();
                                                $estimatedDate = $record->date_estimee;

                                                if ($record->statut === 'livre') return 'success';
                                                if ($estimatedDate->isPast()) return 'danger';
                                                if ($estimatedDate->diffInDays($now) <= 2) return 'warning';
                                                return 'info';
                                            })
                                            ->placeholder('Non définie'),

                                        TextEntry::make('created_at')
                                            ->label('Créé le')
                                            ->dateTime('d/m/Y à H:i')
                                            ->icon('heroicon-m-plus-circle')
                                            ->color('gray'),

                                        TextEntry::make('updated_at')
                                            ->label('Mis à jour le')
                                            ->dateTime('d/m/Y à H:i')
                                            ->icon('heroicon-m-pencil-square')
                                            ->color('gray'),
                                    ]),
                            ])
                            ->collapsible()
                            ->persistCollapsed(),

                        Section::make('📊 Informations système')
                            ->description('Données techniques et métadonnées')
                            ->icon('heroicon-m-cog-6-tooth')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Date de création complète')
                                            ->dateTime('d/m/Y à H:i:s')
                                            ->since()
                                            ->icon('heroicon-m-clock'),

                                        TextEntry::make('updated_at')
                                            ->label('Dernière modification')
                                            ->dateTime('d/m/Y à H:i:s')
                                            ->since()
                                            ->icon('heroicon-m-arrow-path'),
                                    ]),
                            ])
                            ->collapsible()
                            ->collapsed()
                            ->persistCollapsed(),
                    ]),
                Tables\Actions\EditAction::make()
                    ->label('Modifier')
                    ->icon('heroicon-m-pencil-square')
                    ->color('warning'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

 
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('📦 Détails de la livraison')
                    ->description('Informations principales de la livraison')
                    ->icon('heroicon-m-truck')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('ID Livraison')
                                    ->copyable()
                                    ->copyMessage('ID copié!')
                                    ->copyMessageDuration(1500)
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('order.id')
                                    ->label('Commande')
                                    ->badge()
                                    ->color('blue')
                                    ->url(fn($record) => $record->order ? route('filament.admin.resources.orders.view', $record->order) : null),

                                TextEntry::make('statut')
                                    ->label('Statut')
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'en_preparation' => 'En préparation',
                                        'en_transit' => 'En transit',
                                        'livre' => 'Livré',
                                        'retarde' => 'Retardé'
                                    })
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'en_preparation' => 'warning',
                                        'en_transit' => 'info',
                                        'livre' => 'success',
                                        'retarde' => 'danger'
                                    })
                                    ->icon(fn(string $state): string => match ($state) {
                                        'en_preparation' => 'heroicon-m-clock',
                                        'en_transit' => 'heroicon-m-truck',
                                        'livre' => 'heroicon-m-check-circle',
                                        'retarde' => 'heroicon-m-exclamation-triangle'
                                    }),
                            ]),

                        TextEntry::make('adresse_livraison')
                            ->label('Adresse de livraison')
                            ->columnSpanFull()
                            ->icon('heroicon-m-map-pin')
                            ->copyable()
                            ->copyMessage('Adresse copiée!')
                            ->placeholder('Aucune adresse spécifiée'),

                        TextEntry::make('transporteur')
                            ->label('Transporteur')
                            ->icon('heroicon-m-building-office-2')
                            ->badge()
                            ->color('indigo')
                            ->placeholder('Non spécifié'),
                    ])
                    ->collapsible()
                    ->persistCollapsed()
                    ->columns(1),

                Section::make('📅 Informations temporelles')
                    ->description('Dates et délais de livraison')
                    ->icon('heroicon-m-calendar')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('date_estimee')
                                    ->label('Date estimée')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-m-calendar-days')
                                    ->color(function ($record) {
                                        if (!$record->date_estimee) return 'gray';
                                        $now = now();
                                        $estimatedDate = $record->date_estimee;

                                        if ($record->statut === 'livre') return 'success';
                                        if ($estimatedDate->isPast()) return 'danger';
                                        if ($estimatedDate->diffInDays($now) <= 2) return 'warning';
                                        return 'info';
                                    })
                                    ->placeholder('Non définie'),

                                TextEntry::make('created_at')
                                    ->label('Créé le')
                                    ->dateTime('d/m/Y à H:i')
                                    ->icon('heroicon-m-plus-circle')
                                    ->color('gray'),

                                TextEntry::make('updated_at')
                                    ->label('Mis à jour le')
                                    ->dateTime('d/m/Y à H:i')
                                    ->icon('heroicon-m-pencil-square')
                                    ->color('gray'),
                            ]),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),

                Section::make('📊 Informations système')
                    ->description('Données techniques et métadonnées')
                    ->icon('heroicon-m-cog-6-tooth')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Date de création complète')
                                    ->dateTime('d/m/Y à H:i:s')
                                    ->since()
                                    ->icon('heroicon-m-clock'),

                                TextEntry::make('updated_at')
                                    ->label('Dernière modification')
                                    ->dateTime('d/m/Y à H:i:s')
                                    ->since()
                                    ->icon('heroicon-m-arrow-path'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->persistCollapsed(),
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
            'index' => Pages\ListDeliveries::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            DeliveryStatsWidget::class,
        ];
    }
}
