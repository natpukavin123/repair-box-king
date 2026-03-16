<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'contact_person', 'phone', 'email', 'address', 'notes', 'status',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function supplierReturns()
    {
        return $this->hasMany(SupplierReturn::class);
    }
}
