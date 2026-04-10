<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaGroup extends Model
{
    protected $table = 'wa_groups';

    protected $fillable = ['name', 'wa_id', 'type', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(WaMessageLog::class, 'group_wa_id', 'wa_id');
    }
}
