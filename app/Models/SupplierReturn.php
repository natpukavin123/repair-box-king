<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierReturn extends Model
{
    protected $fillable = ['supplier_id', 'product_id', 'quantity', 'return_amount', 'status'];

    protected $casts = ['return_amount' => 'decimal:2'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
