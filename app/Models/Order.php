<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'order_number', // ✅ ici
        'status',
        'total',
        'shipping_address',
        'shipping_city',
        'shipping_country',
        'shipping_zip',
        'date_commande', // si utilisé
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });

        static::creating(function ($order) {
            if (empty($order->id)) {
                $order->id = (string) Str::uuid();
            }

            // Génération auto du numéro de commande (ex: 20250710001)
            $today = now()->format('Ymd');
            $countToday = self::whereDate('created_at', now()->toDateString())->count() + 1;
            $order->order_number = $today . str_pad($countToday, 3, '0', STR_PAD_LEFT);
        });
    }
}
