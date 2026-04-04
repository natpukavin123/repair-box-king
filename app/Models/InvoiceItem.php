<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'item_type', 'product_id', 'service_id',
        'item_name', 'quantity', 'price', 'mrp', 'total',
        'is_linked', 'linked_id',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'mrp'       => 'decimal:2',
        'total'     => 'decimal:2',
        'is_linked' => 'boolean',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
