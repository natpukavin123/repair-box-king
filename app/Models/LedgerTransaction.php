<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerTransaction extends Model
{
    protected $fillable = [
        'transaction_type', 'reference_module', 'reference_id',
        'amount', 'payment_method', 'direction', 'description', 'created_by',
    ];

    protected $casts = ['amount' => 'decimal:2'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
