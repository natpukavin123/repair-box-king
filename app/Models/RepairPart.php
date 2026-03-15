<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairPart extends Model
{
    protected $fillable = ['repair_id', 'part_id', 'product_id', 'quantity', 'cost_price'];

    protected $casts = ['cost_price' => 'decimal:2'];

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
