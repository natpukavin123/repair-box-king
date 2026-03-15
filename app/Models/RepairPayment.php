<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairPayment extends Model
{
    protected $fillable = ['repair_id', 'payment_type', 'amount', 'payment_method', 'reference_number', 'direction', 'notes'];

    protected $casts = ['amount' => 'decimal:2'];

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }
}
