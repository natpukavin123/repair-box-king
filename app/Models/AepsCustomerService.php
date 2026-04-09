<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AepsCustomerService extends Model
{
    protected $fillable = [
        'customer_id', 'customer_name', 'aadhaar_last4', 'service_type',
        'amount', 'bank_name', 'transaction_ref',
        'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
