<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ProductDetailResource extends ProductResource
{
    public function toArray($request): array
    {
        $baseData = parent::toArray($request);

        // Ajouter des champs spécifiques au détail si nécessaire
        return array_merge($baseData, [
            // Informations supplémentaires pour la vue détaillée
            'specifications' => $this->details->map(fn($detail) => [
                'label' => $detail->label,
                'value' => $detail->value,
            ])->toArray(),
            'full_description' => $this->about?->description,
            'available_sizes' => $this->sizes->map(fn($size) => [
                'id' => $size->id,
                'size' => $size->size,
                'stock' => $size->stock ?? 0,
            ])->toArray(),
            'available_colors' => $this->colors->map(fn($color) => [
                'id' => $color->id,
                'name' => $color->name,
                'hex' => $color->hex ?? null,
            ])->toArray(),
        ]);
    }
}
