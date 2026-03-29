<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
    protected $fillable = [
        'customer_id', 'provider_id', 'mobile_number', 'plan_name',
        'recharge_amount', 'payment_method', 'transaction_id', 'status',
    ];

    protected $casts = [
        'recharge_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function provider()
    {
        return $this->belongsTo(RechargeProvider::class, 'provider_id');
    }
}
