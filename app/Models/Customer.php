<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'mobile_number', 'email', 'address', 'notes',
        'loyalty_points', 'total_spent', 'last_visit',
    ];

    protected $casts = [
        'last_visit' => 'datetime',
        'total_spent' => 'decimal:2',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function repairs()
    {
        return $this->hasMany(Repair::class);
    }

    public function recharges()
    {
        return $this->hasMany(Recharge::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
