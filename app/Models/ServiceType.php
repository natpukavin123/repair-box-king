<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['name', 'default_price', 'description', 'quick_fills', 'status', 'image', 'thumbnail'];

    protected $casts = [
        'quick_fills' => 'array',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
