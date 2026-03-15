<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'service_type_id', 'customer_id', 'vendor_id', 'description',
        'vendor_cost', 'customer_charge', 'profit', 'status',
    ];

    protected $casts = [
        'vendor_cost' => 'decimal:2',
        'customer_charge' => 'decimal:2',
        'profit' => 'decimal:2',
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
