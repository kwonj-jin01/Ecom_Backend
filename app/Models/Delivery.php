<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $fillable = [
        'order_id',
        'adresse_livraison',
        'transporteur',
        'statut',
        'date_estimee',
    ];

    protected $casts = [
        'date_estimee' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatutBadgeAttribute(): string
    {
        return match ($this->statut) {
            'en_preparation' => 'warning',
            'en_transit' => 'info',
            'livre' => 'success',
            'retarde' => 'danger'
        };
    }
}
