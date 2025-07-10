<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\Grid;
use Filament\Support\Enums\FontWeight;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Catalogue';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations principales')
                    ->icon('heroicon-o-information-circle')
                    ->description('Informations de base du produit')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom du produit')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, callable $set) => $set('title', $state))
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('title')
                                    ->label('Titre affiché')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('category_id')
                                    ->label('Catégorie')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                    ]),

                                Forms\Components\TextInput::make('brand')
                                    ->label('Marque')
                                    ->maxLength(255)
                                    ->datalist([
                                        'Nike', 'Adidas', 'Puma', 'Reebok', 'New Balance'
                                    ]),

                                Forms\Components\Select::make('gender')
                                    ->label('Genre')
                                    ->options([
                                        'homme' => 'Homme',
                                        'femme' => 'Femme',
                                        'unisexe' => 'Unisexe',
                                    ])
                                    ->required()
                                    ->native(false),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Description')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Description du produit')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Prix et Stock')
                    ->icon('heroicon-o-currency-euro')
                    ->description('Gestion des prix et du stock')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('original_price')
                                    ->label('Prix original')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step(0.01)
                                    ->minValue(0),

                                Forms\Components\TextInput::make('discount_percentage')
                                    ->label('Remise (%)')
                                    ->numeric()
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $originalPrice = $get('original_price');
                                        if ($originalPrice && $state) {
                                            $discountedPrice = $originalPrice * (1 - $state / 100);
                                            $set('price', round($discountedPrice, 2));
                                            $set('discount', round($originalPrice - $discountedPrice, 2));
                                        }
                                    }),

                                Forms\Components\TextInput::make('price')
                                    ->label('Prix final')
                                    ->numeric()
                                    ->prefix('€')
                                    ->required()
                                    ->step(0.01)
                                    ->minValue(0),

                                Forms\Components\TextInput::make('stock')
                                    ->label('Stock disponible')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->suffixIcon('heroicon-o-cube'),

                                Forms\Components\TextInput::make('rating')
                                    ->label('Note moyenne')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(5)
                                    ->step(0.1)
                                    ->suffixIcon('heroicon-o-star'),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Images')
                    ->icon('heroicon-o-photo')
                    ->description('Images du produit')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                FileUpload::make('thumbnail')
                                    ->label('Image principale')
                                    ->image()
                                    ->directory('products/thumbnails')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                        '4:3',
                                        '16:9',
                                    ])
                                    ->optimize('webp')
                                    ->resize(50),

                                FileUpload::make('image')
                                    ->label('Image détaillée')
                                    ->image()
                                    ->directory('products/images')
                                    ->imageEditor()
                                    ->optimize('webp')
                                    ->resize(50),

                                FileUpload::make('hover_image')
                                    ->label('Image au survol')
                                    ->image()
                                    ->directory('products/hover')
                                    ->imageEditor()
                                    ->optimize('webp')
                                    ->resize(50),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Options produit')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_new')
                                    ->label('Nouveau produit')
                                    ->helperText('Affiche le badge "Nouveau"'),

                                Forms\Components\Toggle::make('is_best_seller')
                                    ->label('Meilleure vente')
                                    ->helperText('Affiche le badge "Best Seller"'),

                                Forms\Components\Toggle::make('in_stock')
                                    ->label('En stock')
                                    ->default(true)
                                    ->helperText('Disponible à la vente'),

                                Forms\Components\Toggle::make('is_on_sale')
                                    ->label('En promotion')
                                    ->helperText('Affiche le badge "Promo"'),

                                Forms\Components\TextInput::make('promotion')
                                    ->label('Texte promotionnel')
                                    ->maxLength(255)
                                    ->placeholder('Ex: -20% ce weekend !')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Tailles disponibles')
                    ->icon('heroicon-o-arrows-pointing-out')
                    ->schema([
                        Repeater::make('sizes')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('size')
                                    ->label('Taille')
                                    ->options([
                                        'XS' => 'XS',
                                        'S' => 'S',
                                        'M' => 'M',
                                        'L' => 'L',
                                        'XL' => 'XL',
                                        'XXL' => 'XXL',
                                    ])
                                    ->required()
                                    ->native(false),
                            ])
                            ->columns(1)
                            ->addActionLabel('Ajouter une taille')
                            ->reorderable()
                            ->collapsed(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Couleurs disponibles')
                    ->icon('heroicon-o-swatch')
                    ->schema([
                        Repeater::make('colors')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom de la couleur')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Rouge cardinal'),
                            ])
                            ->columns(1)
                            ->addActionLabel('Ajouter une couleur')
                            ->reorderable()
                            ->collapsed(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Détails techniques')
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        Repeater::make('details')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Caractéristique')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Matière'),
                                Forms\Components\TextInput::make('value')
                                    ->label('Valeur')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: 100% Coton'),
                            ])
                            ->columns(2)
                            ->addActionLabel('Ajouter un détail')
                            ->reorderable()
                            ->collapsed(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Galerie d\'images')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Repeater::make('images')
                            ->relationship()
                            ->schema([
                                FileUpload::make('url')
                                    ->label('Image')
                                    ->image()
                                    ->directory('products/gallery')
                                    ->required()
                                    ->imageEditor()
                                    ->optimize('webp')
                                    ->resize(50),
                            ])
                            ->columns(1)
                            ->addActionLabel('Ajouter une image')
                            ->maxItems(10)
                            ->reorderable()
                            ->collapsed(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Description détaillée')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\RichEditor::make('about.content')
                            ->label('À propos du produit')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                                'blockquote',
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Image')
                    ->circular()
                    ->size(60)
                    ->defaultImageUrl(url('/images/placeholder.png')),

                TextColumn::make('name')
                    ->label('Produit')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Product $record): string => $record->brand ?? 'Aucune marque'),

                BadgeColumn::make('category.name')
                    ->label('Catégorie')
                    ->sortable()
                    ->searchable()
                    ->colors([
                        'primary' => 'Vêtements',
                        'success' => 'Chaussures',
                        'warning' => 'Accessoires',
                        'danger' => 'Sport',
                    ]),

                TextColumn::make('price')
                    ->label('Prix')
                    ->money('EUR')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                BadgeColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->colors([
                        'danger' => static fn ($state): bool => $state < 5,
                        'warning' => static fn ($state): bool => $state >= 5 && $state < 20,
                        'success' => static fn ($state): bool => $state >= 20,
                    ])
                    ->formatStateUsing(fn ($state) => $state . ' unités'),

                BadgeColumn::make('gender')
                    ->label('Genre')
                    ->colors([
                        'primary' => 'homme',
                        'danger' => 'femme',
                        'success' => 'unisexe',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                IconColumn::make('in_stock')
                    ->label('Disponible')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                BadgeColumn::make('status')
                    ->label('Statut')
                    ->getStateUsing(function (Product $record) {
                        if ($record->is_new) return 'Nouveau';
                        if ($record->is_best_seller) return 'Best Seller';
                        if ($record->is_on_sale) return 'Promo';
                        return 'Normal';
                    })
                    ->colors([
                        'success' => 'Nouveau',
                        'warning' => 'Best Seller',
                        'danger' => 'Promo',
                        'secondary' => 'Normal',
                    ]),

                TextColumn::make('rating')
                    ->label('Note')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->suffix('/5')
                    ->icon('heroicon-o-star')
                    ->color('warning'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('brand')
                    ->label('Marque')
                    ->options(function () {
                        return Product::query()
                            ->whereNotNull('brand')
                            ->distinct()
                            ->pluck('brand', 'brand')
                            ->toArray();
                    })
                    ->searchable()
                    ->multiple(),

                SelectFilter::make('gender')
                    ->label('Genre')
                    ->options([
                        'homme' => 'Homme',
                        'femme' => 'Femme',
                        'unisexe' => 'Unisexe',
                    ])
                    ->multiple(),

                Filter::make('stock_status')
                    ->label('Statut du stock')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'out_of_stock' => 'Rupture de stock',
                                'low_stock' => 'Stock faible (< 5)',
                                'medium_stock' => 'Stock moyen (5-20)',
                                'high_stock' => 'Stock élevé (> 20)',
                            ])
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['status'],
                            function (Builder $query, $status) {
                                return match ($status) {
                                    'out_of_stock' => $query->where('stock', '=', 0),
                                    'low_stock' => $query->where('stock', '>', 0)->where('stock', '<', 5),
                                    'medium_stock' => $query->where('stock', '>=', 5)->where('stock', '<=', 20),
                                    'high_stock' => $query->where('stock', '>', 20),
                                    default => $query,
                                };
                            }
                        );
                    }),

                Filter::make('price_range')
                    ->label('Gamme de prix')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('Prix minimum')
                            ->numeric()
                            ->prefix('€'),
                        Forms\Components\TextInput::make('price_to')
                            ->label('Prix maximum')
                            ->numeric()
                            ->prefix('€'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),

                TernaryFilter::make('is_new')
                    ->label('Nouveau produit')
                    ->placeholder('Tous les produits')
                    ->trueLabel('Nouveaux uniquement')
                    ->falseLabel('Anciens uniquement'),

                TernaryFilter::make('is_best_seller')
                    ->label('Meilleure vente')
                    ->placeholder('Tous les produits')
                    ->trueLabel('Best sellers uniquement')
                    ->falseLabel('Autres produits'),

                TernaryFilter::make('is_on_sale')
                    ->label('En promotion')
                    ->placeholder('Tous les produits')
                    ->trueLabel('En promo uniquement')
                    ->falseLabel('Prix normal'),

                TernaryFilter::make('in_stock')
                    ->label('Disponibilité')
                    ->placeholder('Tous les produits')
                    ->trueLabel('En stock uniquement')
                    ->falseLabel('Rupture de stock'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('updateStock')
                        ->label('Mettre à jour le stock')
                        ->icon('heroicon-o-cube')
                        ->color('primary')
                        ->form([
                            Forms\Components\TextInput::make('stock')
                                ->label('Nouveau stock')
                                ->numeric()
                                ->required()
                                ->minValue(0),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each->update(['stock' => $data['stock']]);
                        }),

                    Tables\Actions\BulkAction::make('togglePromotion')
                        ->label('Basculer promotion')
                        ->icon('heroicon-o-tag')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_on_sale' => !$record->is_on_sale]);
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers peuvent être ajoutés ici
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ProductResource\Widgets\ProductStatsWidget::class,
            ProductResource\Widgets\BestSellersWidget::class,
            ProductResource\Widgets\CategoryChartWidget::class,
        ];
    }
}
