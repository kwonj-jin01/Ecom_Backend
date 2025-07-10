<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'E-commerce';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Commandes';
    protected static ?string $pluralModelLabel = 'Commandes';
    protected static ?string $modelLabel = 'Commande';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations de la commande')
                ->description('Détails principaux de la commande')
                ->icon('heroicon-o-document-text')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Date de commande')
                            ->required()
                            ->default(now())
                            ->prefixIcon('heroicon-o-calendar-days')
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->required()
                            ->options([
                                'en_attente' => 'En attente',
                                'confirmée' => 'Confirmée',
                                'préparation' => 'En préparation',
                                'expédiée' => 'Expédiée',
                                'livrée' => 'Livrée',
                                'annulée' => 'Annulée',
                            ])
                            ->default('en_attente')
                            ->prefixIcon('heroicon-o-flag')
                            ->native(false),
                    ]),

                    Forms\Components\Select::make('user_id')
                        ->label('Client')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->prefixIcon('heroicon-o-user')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->label('Nom complet')
                                ->required(),
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required(),
                            Forms\Components\TextInput::make('phone')
                                ->label('Téléphone')
                                ->tel(),
                        ]),
                ]),

            Forms\Components\Section::make('Détails financiers')
                ->description('Informations sur les montants et paiements')
                ->icon('heroicon-o-currency-dollar')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Sous-total')
                            ->numeric()
                            ->prefix('XOF')
                            ->readOnly()
                            ->default(0),

                        Forms\Components\TextInput::make('tax_amount')
                            ->label('Taxes')
                            ->numeric()
                            ->prefix('XOF')
                            ->default(0),

                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('XOF')
                            ->required()
                            ->default(0),
                    ]),
                ]),

            Forms\Components\Section::make('Informations de livraison')
                ->description('Adresse et méthode de livraison')
                ->icon('heroicon-o-truck')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('shipping_address')
                            ->label('Adresse de livraison')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('shipping_city')
                            ->label('Ville'),

                        Forms\Components\TextInput::make('shipping_postal_code')
                            ->label('Code postal'),
                    ]),
                ]),

            Forms\Components\Section::make('Notes')
                ->description('Commentaires et notes internes')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->collapsible()
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->label('Notes internes')
                        ->rows(3)
                        ->placeholder('Commentaires sur la commande...'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('N° Commande')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->weight(FontWeight::Medium)
                    ->copyable()
                    ->copyMessage('Nom du client copié!')
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable()
                    ->icon('heroicon-o-envelope'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->description(fn($record) => $record->created_at->format('d/m/Y à H:i'))
                    ->icon('heroicon-o-calendar-days'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'secondary' => 'en_attente',
                        'warning' => 'confirmée',
                        'info' => 'préparation',
                        'primary' => 'expédiée',
                        'success' => 'livrée',
                        'danger' => 'annulée',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'en_attente',
                        'heroicon-o-check-circle' => 'confirmée',
                        'heroicon-o-cog-6-tooth' => 'préparation',
                        'heroicon-o-truck' => 'expédiée',
                        'heroicon-o-hand-thumb-up' => 'livrée',
                        'heroicon-o-x-circle' => 'annulée',
                    ])
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'en_attente' => 'En attente',
                        'confirmée' => 'Confirmée',
                        'préparation' => 'En préparation',
                        'expédiée' => 'Expédiée',
                        'livrée' => 'Livrée',
                        'annulée' => 'Annulée',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('XOF')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar'),

                Tables\Columns\TextColumn::make('payment.method')
                    ->label('Méthode de paiement')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'espece' => 'warning',
                        'carte' => 'success',
                        'mobile_money' => 'info',
                        'paypal' => 'purple',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'espece' => 'Espèces',
                        'carte' => 'Carte',
                        'mobile_money' => 'Mobile Money',
                        'paypal' => 'PayPal',
                        default => 'Non défini',
                    }),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrer par statut')
                    ->options([
                        'en_attente' => 'En attente',
                        'confirmée' => 'Confirmée',
                        'préparation' => 'En préparation',
                        'expédiée' => 'Expédiée',
                        'livrée' => 'Livrée',
                        'annulée' => 'Annulée',
                    ])
                    ->multiple()
                    ->preload(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Du'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Au'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn($q) => $q->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'], fn($q) => $q->whereDate('created_at', '<=', $data['created_until']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Du: ' . Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Au: ' . Carbon::parse($data['created_until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\TernaryFilter::make('is_urgent')
                    ->label('Commandes urgentes')
                    ->placeholder('Toutes les commandes')
                    ->trueLabel('Urgentes seulement')
                    ->falseLabel('Non urgentes seulement'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info')
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                ])
                    ->label('Actions')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label('Changer le statut')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Nouveau statut')
                                ->options([
                                    'en_attente' => 'En attente',
                                    'confirmée' => 'Confirmée',
                                    'préparation' => 'En préparation',
                                    'expédiée' => 'Expédiée',
                                    'livrée' => 'Livrée',
                                    'annulée' => 'Annulée',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['status' => $data['status']]);
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s')
            ->deferLoading()
            ->extremePaginationLinks();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations de la commande')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Infolists\Components\Grid::make(3)->schema([
                            Infolists\Components\TextEntry::make('id')
                                ->label('Numéro de commande')
                                ->formatStateUsing(fn($state) => '#' . str_pad($state, 7, '0', STR_PAD_LEFT))
                                ->weight(FontWeight::Bold)
                                ->color('primary'),

                            Infolists\Components\TextEntry::make('created_at')
                                ->label('Date de création')
                                ->dateTime('d/m/Y à H:i')
                                ->icon('heroicon-o-calendar-days'),

                            Infolists\Components\TextEntry::make('updated_at')
                                ->label('Dernière modification')
                                ->dateTime('d/m/Y à H:i')
                                ->since()
                                ->icon('heroicon-o-clock'),
                        ]),

                        Infolists\Components\Grid::make(2)->schema([
                            Infolists\Components\TextEntry::make('status')
                                ->label('Statut')
                                ->badge()
                                ->color(fn($state) => match ($state) {
                                    'en_attente' => 'secondary',
                                    'confirmée' => 'warning',
                                    'préparation' => 'info',
                                    'expédiée' => 'primary',
                                    'livrée' => 'success',
                                    'annulée' => 'danger',
                                    default => 'secondary',
                                })
                                ->icon(fn($state) => match ($state) {
                                    'en_attente' => 'heroicon-o-clock',
                                    'confirmée' => 'heroicon-o-check-circle',
                                    'préparation' => 'heroicon-o-cog-6-tooth',
                                    'expédiée' => 'heroicon-o-truck',
                                    'livrée' => 'heroicon-o-hand-thumb-up',
                                    'annulée' => 'heroicon-o-x-circle',
                                    default => 'heroicon-o-question-mark-circle',
                                }),

                            Infolists\Components\IconEntry::make('is_urgent')
                                ->label('Urgent')
                                ->boolean()
                                ->trueIcon('heroicon-o-exclamation-triangle')
                                ->falseIcon('heroicon-o-minus')
                                ->trueColor('danger')
                                ->falseColor('gray'),
                        ]),
                    ]),

                Infolists\Components\Section::make('Informations client')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Infolists\Components\Grid::make(2)->schema([
                            Infolists\Components\TextEntry::make('user.name')
                                ->label('Nom du client')
                                ->weight(FontWeight::Medium)
                                ->copyable()
                                ->icon('heroicon-o-user'),

                            Infolists\Components\TextEntry::make('user.email')
                                ->label('Email')
                                ->copyable()
                                ->icon('heroicon-o-envelope'),

                            Infolists\Components\TextEntry::make('user.phone')
                                ->label('Téléphone')
                                ->copyable()
                                ->icon('heroicon-o-phone'),

                            Infolists\Components\TextEntry::make('user.created_at')
                                ->label('Client depuis')
                                ->dateTime('d/m/Y')
                                ->icon('heroicon-o-calendar-days'),
                        ]),
                    ]),

                Infolists\Components\Section::make('Détails financiers')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Infolists\Components\Grid::make(3)->schema([
                            Infolists\Components\TextEntry::make('subtotal')
                                ->label('Sous-total')
                                ->money('XOF')
                                ->weight(FontWeight::Medium),

                            Infolists\Components\TextEntry::make('tax_amount')
                                ->label('Taxes')
                                ->money('XOF'),

                            Infolists\Components\TextEntry::make('total')
                                ->label('Total')
                                ->money('XOF')
                                ->weight(FontWeight::Bold)
                                ->color('success')
                                ->size('lg'),
                        ]),

                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Méthode')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'cash' => 'warning',
                                'card' => 'success',
                                'mobile' => 'info',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn($state) => match ($state) {
                                'cash' => 'Espèces',
                                'card' => 'Carte bancaire',
                                'mobile' => 'Mobile Money',
                                default => 'Non défini',
                            }),
                    ]),

                Infolists\Components\Section::make('Informations de livraison')
                    ->icon('heroicon-o-truck')
                    ->schema([
                        Infolists\Components\Grid::make(2)->schema([
                            Infolists\Components\TextEntry::make('shipping_address')
                                ->label('Adresse de livraison')
                                ->columnSpanFull(),

                            Infolists\Components\TextEntry::make('shipping_city')
                                ->label('Ville'),

                            Infolists\Components\TextEntry::make('shipping_postal_code')
                                ->label('Code postal'),
                        ]),
                    ]),

                Infolists\Components\Section::make('Notes')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notes internes')
                            ->placeholder('Aucune note')
                            ->markdown(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Relations pour OrderItems, Payments, etc.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'en_attente')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'en_attente')->count() > 0 ? 'danger' : 'primary';
    }
}
