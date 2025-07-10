<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Modifier')
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            Actions\DeleteAction::make()
                ->label('Supprimer')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    Grid::make(2)
                        ->schema([
                            Section::make('Informations générales')
                                ->schema([
                                    TextEntry::make('name')
                                        ->label('Nom de la catégorie')
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold)
                                        ->color('primary')
                                        ->icon('heroicon-o-tag'),

                                    TextEntry::make('description')
                                        ->label('Description')
                                        ->placeholder('Aucune description')
                                        ->columnSpanFull(),

                                    TextEntry::make('products_count')
                                        ->label('Nombre de produits')
                                        ->badge()
                                        ->color(fn (string $state): string => match (true) {
                                            $state === '0' => 'gray',
                                            $state < '5' => 'warning',
                                            $state < '10' => 'success',
                                            default => 'primary',
                                        })
                                        ->icon('heroicon-o-cube')
                                        ->getStateUsing(fn ($record) => $record->products()->count()),
                                ])
                                ->columns(2)
                                ->columnSpan(2),
                        ]),

                    Section::make('Dates')
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Créée le')
                                ->dateTime('d/m/Y à H:i')
                                ->icon('heroicon-o-calendar-plus'),

                            TextEntry::make('updated_at')
                                ->label('Modifiée le')
                                ->dateTime('d/m/Y à H:i')
                                ->icon('heroicon-o-calendar')
                                ->since(),
                        ])
                        ->columnSpan(1),
                ])
                ->from('lg'),

                Section::make('Produits associés')
                    ->schema([
                        TextEntry::make('products.name')
                            ->label('Liste des produits')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->limitList(10)
                            ->expandableLimitedList()
                            ->placeholder('Aucun produit associé à cette catégorie')
                            ->icon('heroicon-o-cube'),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => $record->products()->count() === 0),

                Section::make('Statistiques')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('total_products')
                                    ->label('Total produits')
                                    ->getStateUsing(fn ($record) => $record->products()->count())
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-cube'),

                                TextEntry::make('active_products')
                                    ->label('Produits actifs')
                                    ->getStateUsing(fn ($record) => $record->products()->where('is_active', true)->count())
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle'),

                                TextEntry::make('inactive_products')
                                    ->label('Produits inactifs')
                                    ->getStateUsing(fn ($record) => $record->products()->where('is_active', false)->count())
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-x-circle'),
                            ])
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public function getTitle(): string
    {
        return 'Catégorie : ' . $this->record->name;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CategoryResource\Widgets\CategoryDetailWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Widgets en bas de page si nécessaire
        ];
    }
}
