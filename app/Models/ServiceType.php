<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['name', 'default_price', 'description', 'status'];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
