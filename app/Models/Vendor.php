<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'gstin', 'specialization', 'status'];

    public function repairVendors()
    {
        return $this->hasMany(RepairVendor::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
