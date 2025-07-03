<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'name',
        'description',
        'price',
        'discount_percentage',
        'rating',
        'stock',
        'brand',
        'gender',
        'thumbnail',
        'image',
        'hover_image',
        'is_new',
        'is_best_seller',
        'in_stock',
        'is_on_sale',
        'original_price',
        'discount',
        'promotion',
        'category_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount' => 'decimal:2',
        'rating' => 'decimal:1',
        'is_new' => 'boolean',
        'is_best_seller' => 'boolean',
        'in_stock' => 'boolean',
        'is_on_sale' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function colors()
    {
        return $this->hasMany(ProductColor::class);
    }

    public function details()
    {
        return $this->hasMany(ProductDetail::class);
    }

    public function about()
    {
        return $this->hasOne(About::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
