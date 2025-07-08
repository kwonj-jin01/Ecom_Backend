<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Expense extends Model
{
    use HasFactory;

    // Si tu utilises un UUID comme clé primaire
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Générer automatiquement un UUID si vide
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
