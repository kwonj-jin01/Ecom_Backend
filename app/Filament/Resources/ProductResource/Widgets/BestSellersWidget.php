<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Support\Enums\FontWeight;

class BestSellersWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 10 - Meilleures Ventes';
    protected static ?string $description = 'Produits les plus performants';
    protected static ?string $icon = 'heroicon-o-trophy';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('is_best_seller', true)
                    ->orWhereRaw('rating >= 4.0')
                    ->orWhereRaw('stock < (SELECT AVG(stock) FROM products WHERE stock > 0)')
                    ->orderByDesc('rating')
                    ->orderByDesc('is_best_seller')
                    ->orderBy('stock', 'asc')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('rank')
                    ->label('#')
                    ->rowIndex()
                    ->alignCenter()
                    ->size(TextColumn\TextColumnSize::Large)
                    ->weight(FontWeight::Bold)
                    ->color(fn ($rowLoop) => match ($rowLoop->index + 1) {
                        1 => 'warning',
                        2 => 'gray',
                        3 => 'danger',
                        default => 'primary'
                    }),

                ImageColumn::make('thumbnail')
                    ->label('Image')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(url('/images/placeholder.png')),

                TextColumn::make('name')
                    ->label('Produit')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Product $record): string => $record->brand ?? 'Sans marque')
                    ->wrap(),

                BadgeColumn::make('category.name')
                    ->label('Catégorie')
                    ->colors([
                        'primary' => 'Vêtements',
                        'success' => 'Chaussures',
                        'warning' => 'Accessoires',
                        'danger' => 'Sport',
                    ]),

                TextColumn::make('price')
                    ->label('Prix')
                    ->money('EUR')
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                BadgeColumn::make('stock')
                    ->label('Stock')
                    ->colors([
                        'danger' => static fn ($state): bool => $state < 5,
                        'warning' => static fn ($state): bool => $state >= 5 && $state < 20,
                        'success' => static fn ($state): bool => $state >= 20,
                    ])
                    ->formatStateUsing(fn ($state) => $state . ' unités'),

                TextColumn::make('rating')
                    ->label('Note')
                    ->numeric(decimalPlaces: 1)
                    ->suffix('/5')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->weight(FontWeight::Bold),

                BadgeColumn::make('badges')
                    ->label('Badges')
                    ->getStateUsing(function (Product $record) {
                        $badges = [];
                        if ($record->is_best_seller) $badges[] = 'Best Seller';
                        if ($record->is_new) $badges[] = 'Nouveau';
                        if ($record->is_on_sale) $badges[] = 'Promo';
                        return implode(' • ', $badges) ?: 'Aucun';
                    })
                    ->colors([
                        'success' => fn ($state) => str_contains($state, 'Best Seller'),
                        'info' => fn ($state) => str_contains($state, 'Nouveau'),
                        'danger' => fn ($state) => str_contains($state, 'Promo'),
                        'gray' => fn ($state) => $state === 'Aucun',
                    ])
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Ajouté le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Product $record): string =>
                        route('filament.admin.resources.products.view', $record)
                    ),
                Tables\Actions\Action::make('edit')
                    ->label('Modifier')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->url(fn (Product $record): string =>
                        route('filament.admin.resources.products.edit', $record)
                    ),
            ])
            ->striped()
            ->paginated(false)
            ->emptyStateHeading('Aucun produit best-seller')
            ->emptyStateDescription('Aucun produit n\'a encore été marqué comme meilleure vente.')
            ->emptyStateIcon('heroicon-o-trophy');
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10];
    }

    public static function canView(): bool
    {
        return Product::where('is_best_seller', true)->exists() ||
               Product::where('rating', '>=', 4.0)->exists();
    }
}
