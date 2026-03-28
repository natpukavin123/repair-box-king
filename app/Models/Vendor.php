<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'specialization', 'status', 'image', 'thumbnail'];

    public function repairVendors()
    {
        return $this->hasMany(RepairVendor::class);
    }
}
