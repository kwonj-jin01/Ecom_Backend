<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrower_name',
        'borrower_location',
        'amount',
        'type',
        'loan_type',
        'status',
        'unit_number',
        'disbursed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'disbursed_at' => 'datetime',
    ];

    public function scopeDisbursed($query)
    {
        return $query->where('type', 'disbursed');
    }

    public function scopeClosed($query)
    {
        return $query->where('type', 'closed');
    }

    public function scopeClassified($query)
    {
        return $query->where('type', 'classified');
    }

    public function scopeRunningPaid($query)
    {
        return $query->where('type', 'running_paid');
    }

    public function scopeRunningUnpaid($query)
    {
        return $query->where('type', 'running_unpaid');
    }
}
