<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['name', 'default_price', 'sac_code', 'tax_rate_id', 'description', 'status'];

    /**
     * Auto-resolve tax_rate_id from hsn_codes master (type=sac) whenever sac_code
     * is set or changed. Clears tax_rate_id when sac_code is removed.
     */
    protected static function booted(): void
    {
        static::saving(function (ServiceType $serviceType) {
            if ($serviceType->isDirty('sac_code') || !$serviceType->tax_rate_id) {
                if ($serviceType->sac_code) {
                    $sacRecord = HsnCode::where('code', $serviceType->sac_code)
                        ->where('type', 'sac')
                        ->where('is_active', true)
                        ->first();
                    $serviceType->tax_rate_id = $sacRecord?->tax_rate_id;
                } else {
                    $serviceType->tax_rate_id = null;
                }
            }
        });
    }

    /**
     * Lookup the SAC code master record linked to this service type's sac_code string.
     */
    public function sacCode()
    {
        return $this->belongsTo(HsnCode::class, 'sac_code', 'code')
            ->where('type', 'sac');
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    /**
     * Resolve the effective GST percentage for this service type.
     * Priority: tax_rate_id > sac_code master > default rate.
     */
    public function getEffectiveTaxPercentAttribute(): float
    {
        if ($this->taxRate) {
            return (float) $this->taxRate->percentage;
        }
        if ($this->sac_code) {
            $sacRecord = HsnCode::where('code', $this->sac_code)
                ->where('type', 'sac')
                ->where('is_active', true)
                ->first();
            if ($sacRecord?->taxRate) {
                return (float) $sacRecord->taxRate->percentage;
            }
        }
        $default = TaxRate::getDefault();
        return $default ? (float) $default->percentage : 0;
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
