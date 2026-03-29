<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RechargeProvider extends Model
{
    protected $fillable = ['name', 'provider_type', 'status', 'image', 'thumbnail'];

    public function recharges()
    {
        return $this->hasMany(Recharge::class, 'provider_id');
    }
}
