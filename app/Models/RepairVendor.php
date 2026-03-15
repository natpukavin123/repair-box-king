<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairVendor extends Model
{
    protected $fillable = ['repair_id', 'vendor_id', 'vendor_cost', 'sent_date', 'return_date', 'status'];

    protected $casts = [
        'vendor_cost' => 'decimal:2',
        'sent_date' => 'date',
        'return_date' => 'date',
    ];

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
