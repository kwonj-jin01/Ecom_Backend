<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'customer_name',
        'amount',
        'loan_type',
        'date',
        'time',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'datetime',
    ];
}
