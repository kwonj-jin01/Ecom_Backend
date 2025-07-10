<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ProductResource\Widgets\ProductStatsWidget;
use App\Filament\Resources\ProductResource\Widgets\BestSellersWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouveau Produit')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProductStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BestSellersWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous les produits')
                ->badge(Product::count())
                ->badgeColor('primary'),

            'in_stock' => Tab::make('En stock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('in_stock', true))
                ->badge(Product::where('in_stock', true)->count())
                ->badgeColor('success'),

            'out_of_stock' => Tab::make('Rupture de stock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('stock', '<=', 0))
                ->badge(Product::where('stock', '<=', 0)->count())
                ->badgeColor('danger'),

            'low_stock' => Tab::make('Stock faible')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('stock', '>', 0)->where('stock', '<', 5))
                ->badge(Product::where('stock', '>', 0)->where('stock', '<', 5)->count())
                ->badgeColor('warning'),

            'best_sellers' => Tab::make('Meilleures ventes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_best_seller', true))
                ->badge(Product::where('is_best_seller', true)->count())
                ->badgeColor('warning'),

            'new_products' => Tab::make('Nouveaux')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_new', true))
                ->badge(Product::where('is_new', true)->count())
                ->badgeColor('info'),

            'on_sale' => Tab::make('En promotion')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_on_sale', true))
                ->badge(Product::where('is_on_sale', true)->count())
                ->badgeColor('danger'),
        ];
    }

    public function getTitle(): string
    {
        $total = Product::count();
        return "Produits ({$total})";
    }

    public function getSubheading(): string
    {
        $inStock = Product::where('in_stock', true)->count();
        $outOfStock = Product::where('stock', '<=', 0)->count();
        $lowStock = Product::where('stock', '>', 0)->where('stock', '<', 5)->count();

        return "En stock: {$inStock} • Rupture: {$outOfStock} • Stock faible: {$lowStock}";
    }
}
