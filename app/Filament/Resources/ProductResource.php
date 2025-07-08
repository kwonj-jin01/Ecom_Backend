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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'E‑commerce';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations principales')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $set('title', $state)),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category_id')
                            ->label('Catégorie')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('brand')
                            ->label('Marque')
                            ->maxLength(255),

                        Forms\Components\Select::make('gender')
                            ->label('Genre')
                            ->options([
                                'homme' => 'Homme',
                                'femme' => 'Femme',
                                'unisexe' => 'Unisexe',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(4),
                    ]),

                Forms\Components\Section::make('Prix et Stock')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Prix')
                            ->numeric()
                            ->prefix('€')
                            ->required(),

                        Forms\Components\TextInput::make('original_price')
                            ->label('Prix original')
                            ->numeric()
                            ->prefix('€'),

                        Forms\Components\TextInput::make('discount_percentage')
                            ->label('Pourcentage de remise')
                            ->numeric()
                            ->suffix('%')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $originalPrice = $get('original_price');
                                if ($originalPrice && $state) {
                                    $discountedPrice = $originalPrice * (1 - $state / 100);
                                    $set('price', round($discountedPrice, 2));
                                    $set('discount', round($originalPrice - $discountedPrice, 2));
                                }
                            }),

                        Forms\Components\TextInput::make('stock')
                            ->numeric()
                            ->required()
                            ->minValue(0),

                        Forms\Components\TextInput::make('rating')
                            ->label('Note')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.1),
                    ])->columns(3),

                Forms\Components\Section::make('Images')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->label('Image principale')
                            ->image()
                            ->directory('products/thumbnails'),

                        FileUpload::make('image')
                            ->label('Image détaillée')
                            ->image()
                            ->directory('products/images'),

                        FileUpload::make('hover_image')
                            ->label('Image au survol')
                            ->image()
                            ->directory('products/hover'),
                    ])->columns(3),

                Forms\Components\Section::make('Options')
                    ->schema([
                        Forms\Components\Toggle::make('is_new')
                            ->label('Nouveau produit'),

                        Forms\Components\Toggle::make('is_best_seller')
                            ->label('Meilleure vente'),

                        Forms\Components\Toggle::make('in_stock')
                            ->label('En stock')
                            ->default(true),

                        Forms\Components\Toggle::make('is_on_sale')
                            ->label('En promotion'),

                        Forms\Components\TextInput::make('promotion')
                            ->label('Texte de promotion')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Tailles disponibles')
                    ->schema([
                        Repeater::make('sizes')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('size')
                                    ->options([
                                        'XS' => 'XS',
                                        'S' => 'S',
                                        'M' => 'M',
                                        'L' => 'L',
                                        'XL' => 'XL',
                                        'XXL' => 'XXL',
                                    ])
                                    ->required(),
                            ])
                            ->columns(1)
                            ->addActionLabel('Ajouter une taille'),
                    ]),

                Forms\Components\Section::make('Couleurs disponibles')
                    ->schema([
                        Repeater::make('colors')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom de la couleur')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(1)
                            ->addActionLabel('Ajouter une couleur'),
                    ]),

                Forms\Components\Section::make('Détails du produit')
                    ->schema([
                        Repeater::make('details')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Étiquette')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('value')
                                    ->label('Valeur')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->addActionLabel('Ajouter un détail'),
                    ]),

                Forms\Components\Section::make('Images supplémentaires')
                    ->schema([
                        Repeater::make('images')
                            ->relationship()
                            ->schema([
                                FileUpload::make('url')
                                    ->label('Image')
                                    ->image()
                                    ->directory('products/gallery')
                                    ->required(),
                            ])
                            ->columns(1)
                            ->addActionLabel('Ajouter une image')
                            ->maxItems(2), // <- limite max ici

                    ]),

                Forms\Components\Section::make('À propos du produit')
                    ->schema([
                        Forms\Components\Textarea::make('about.content')
                            ->label('Description détaillée')
                            ->rows(6),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('brand')
                    ->label('Marque')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->color(fn($state) => $state < 5 ? 'danger' : ($state < 20 ? 'warning' : 'success')),

                Tables\Columns\IconColumn::make('in_stock')
                    ->label('En stock')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_new')
                    ->label('Nouveau')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_best_seller')
                    ->label('Best seller')
                    ->boolean(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Note')
                    ->numeric(decimalPlaces: 1)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Catégorie'),

                Tables\Filters\Filter::make('Rupture de stock')
                    ->query(fn($query) => $query->where('stock', '<', 1)),

                Tables\Filters\Filter::make('Stock faible')
                    ->query(fn($query) => $query->where('stock', '<', 5)),

                Tables\Filters\TernaryFilter::make('is_new')
                    ->label('Nouveau produit'),

                Tables\Filters\TernaryFilter::make('is_best_seller')
                    ->label('Meilleure vente'),

                Tables\Filters\TernaryFilter::make('is_on_sale')
                    ->label('En promotion'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Vous pouvez ajouter des RelationManagers ici si nécessaire
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
}
