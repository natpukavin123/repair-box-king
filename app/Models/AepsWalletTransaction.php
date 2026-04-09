<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AepsWalletTransaction extends Model
{
    protected $fillable = [
        'type', 'amount', 'direction', 'balance_after',
        'payment_method', 'reference', 'notes', 'created_by',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
