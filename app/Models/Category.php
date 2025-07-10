<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name'];
    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->id)) {
                $category->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
