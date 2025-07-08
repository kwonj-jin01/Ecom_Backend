<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'title'           => $this->title,
            'description'     => $this->description,
            'stock'           => $this->stock,
            'price'           => number_format((float) $this->price, 2, '.', ''),
            'original_price'  => number_format((float) $this->original_price, 2, '.', ''),
            'discount'        => $this->discount ? number_format((float) $this->discount, 2, '.', '') : null,
            'discount_percentage' => $this->discount_percentage ? number_format((float) $this->discount_percentage, 2, '.', '') : null,
            'rating'          => number_format((float) $this->rating, 1, '.', ''),
            'in_stock'        => (bool) $this->in_stock,
            'is_new'          => (bool) $this->is_new,
            'is_best_seller'  => (bool) $this->is_best_seller,
            'is_on_sale'      => (bool) $this->is_on_sale,
            'promotion'       => $this->promotion,
            'brand'           => $this->brand,
            'gender'          => $this->gender,
            'thumbnail'       => $this->thumbnail,
            'hover_image'     => $this->hover_image,
            'image'           => $this->images->first()?->url ?? $this->thumbnail,

            // Relations
            'category'        => $this->category?->name,
            'category_id'     => $this->category_id,
            'sizes'           => $this->sizes->pluck('size')->toArray(),
            'colors'          => $this->colors->pluck('name')->toArray(),
            'details'         => $this->details->map(fn($d) => [
                'label' => $d->label,
                'value' => $d->value
            ])->toArray(),
            'about'           => $this->about?->description,

            'images'          => $this->images->pluck('url')->toArray(),

            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
