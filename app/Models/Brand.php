<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'status', 'image', 'thumbnail', 'models'];

    protected $casts = ['models' => 'array'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
