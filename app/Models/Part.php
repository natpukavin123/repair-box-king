<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    protected $fillable = ['name', 'sku', 'cost_price', 'selling_price', 'hsn_code', 'tax_rate_id', 'status'];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    /**
     * Auto-resolve tax_rate_id from hsn_codes master whenever hsn_code is set
     * or changed. Clears tax_rate_id when hsn_code is removed.
     */
    protected static function booted(): void
    {
        static::saving(function (Part $part) {
            if ($part->isDirty('hsn_code') || !$part->tax_rate_id) {
                if ($part->hsn_code) {
                    $hsnRecord = HsnCode::where('code', $part->hsn_code)
                        ->where('type', 'hsn')
                        ->where('is_active', true)
                        ->first();
                    $part->tax_rate_id = $hsnRecord?->tax_rate_id;
                } else {
                    $part->tax_rate_id = null;
                }
            }
        });
    }

    /**
     * Lookup the HSN code master record linked to this part's hsn_code string.
     * Use: $part->hsnCode->description, $part->hsnCode->taxRate
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
     * Resolve the effective GST percentage for this part.
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
}
