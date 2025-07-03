<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['order_id', 'amount', 'method', 'status', 'transaction_id', 'paid_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
