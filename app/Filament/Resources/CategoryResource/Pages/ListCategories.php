<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\IconPosition;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle catégorie')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }

    public function getTitle(): string
    {
        return 'Gestion des catégories';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CategoryResource\Widgets\CategoryStatsWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Toutes')
                ->icon('heroicon-o-rectangle-stack')
                ->badge(Category::count())
                ->badgeColor('primary'),

            'with_products' => Tab::make('Avec produits')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->has('products'))
                ->badge(Category::has('products')->count())
                ->badgeColor('success'),

            'without_products' => Tab::make('Sans produits')
                ->icon('heroicon-o-exclamation-triangle')
                ->modifyQueryUsing(fn (Builder $query) => $query->doesntHave('products'))
                ->badge(Category::doesntHave('products')->count())
                ->badgeColor('warning'),
        ];
    }
}
