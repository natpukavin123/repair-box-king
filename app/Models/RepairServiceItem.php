<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairServiceItem extends Model
{
    protected $table = 'repair_services';

    protected $fillable = [
        'repair_id', 'service_type_id', 'service_type_name', 'vendor_id',
        'customer_charge', 'vendor_charge',
        'sac_code', 'tax_rate', 'tax_amount',
        'reference_no', 'description',
    ];

    protected $casts = [
        'customer_charge' => 'decimal:2',
        'vendor_charge' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
