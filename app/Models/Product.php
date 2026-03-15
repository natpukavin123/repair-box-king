<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'subcategory_id', 'brand_id', 'name', 'sku',
        'barcode', 'purchase_price', 'mrp', 'selling_price', 'hsn_code', 'tax_rate_id',
        'description', 'status', 'image', 'thumbnail',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Auto-resolve tax_rate_id from hsn_codes master whenever hsn_code is set
     * or changed. Clears tax_rate_id when hsn_code is removed.
     */
    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if ($product->isDirty('hsn_code') || !$product->tax_rate_id) {
                if ($product->hsn_code) {
                    $hsnRecord = HsnCode::where('code', $product->hsn_code)
                        ->where('type', 'hsn')
                        ->where('is_active', true)
                        ->first();
                    $product->tax_rate_id = $hsnRecord?->tax_rate_id;
                } else {
                    $product->tax_rate_id = null;
                }
            }
        });
    }

    /**
     * Lookup the HSN code master record linked to this product's hsn_code string.
     */
    public function hsnCode()
    {
        return $this->belongsTo(HsnCode::class, 'hsn_code', 'code');
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    /**
     * Resolve the effective GST percentage for this product.
     * Priority: tax_rate_id > hsn_code master > default rate.
     */
    public function getEffectiveTaxPercentAttribute(): float
    {
        if ($this->taxRate) {
            return (float) $this->taxRate->percentage;
        }
        if ($this->hsn_code) {
            $hsnRecord = HsnCode::where('code', $this->hsn_code)
                ->where('type', 'hsn')
                ->where('is_active', true)
                ->first();
            if ($hsnRecord?->taxRate) {
                return (float) $hsnRecord->taxRate->percentage;
            }
        }
        $default = TaxRate::getDefault();
        return $default ? (float) $default->percentage : 0;
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getAvailableStockAttribute(): int
    {
        $inv = $this->inventory;
        return $inv ? ($inv->current_stock - $inv->reserved_stock) : 0;
    }
}
