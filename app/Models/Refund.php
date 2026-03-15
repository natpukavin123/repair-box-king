<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'reference_type', 'reference_id', 'refund_amount', 'refund_method', 'reason', 'status',
    ];

    protected $casts = ['refund_amount' => 'decimal:2'];
}
