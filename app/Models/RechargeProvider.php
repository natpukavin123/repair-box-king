<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RechargeProvider extends Model
{
    protected $fillable = ['name', 'provider_type', 'commission_percentage', 'status', 'image', 'thumbnail'];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
    ];

    public function recharges()
    {
        return $this->hasMany(Recharge::class, 'provider_id');
    }
}
