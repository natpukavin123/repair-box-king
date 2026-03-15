<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $fillable = ['name', 'percentage', 'is_default', 'is_active'];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function hsnCodes()
    {
        return $this->hasMany(HsnCode::class);
    }

    public function parts()
    {
        return $this->hasMany(Part::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function serviceTypes()
    {
        return $this->hasMany(ServiceType::class);
    }

    /**
     * Get the default tax rate.
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->where('is_active', true)->first();
    }

    /**
     * Calculate CGST + SGST (intra-state) from this rate.
     * Each is half of the total GST percentage.
     */
    public function getCgstRateAttribute(): float
    {
        return round($this->percentage / 2, 2);
    }

    public function getSgstRateAttribute(): float
    {
        return round($this->percentage / 2, 2);
    }

    /**
     * IGST rate equals the full GST percentage (inter-state).
     */
    public function getIgstRateAttribute(): float
    {
        return (float) $this->percentage;
    }
}
