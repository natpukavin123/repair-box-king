<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HsnCode extends Model
{
    protected $fillable = ['code', 'type', 'description', 'tax_rate_id', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    /**
     * Scope to only HSN codes (goods).
     */
    public function scopeHsn($query)
    {
        return $query->where('type', 'hsn');
    }

    /**
     * Scope to only SAC codes (services).
     */
    public function scopeSac($query)
    {
        return $query->where('type', 'sac');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
