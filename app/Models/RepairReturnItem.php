<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairReturnItem extends Model
{
    protected $fillable = [
        'repair_return_id', 'item_type', 'repair_part_id', 'repair_service_id',
        'item_name', 'quantity', 'unit_price', 'return_amount', 'reason',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'return_amount' => 'decimal:2',
    ];

    public function repairReturn()
    {
        return $this->belongsTo(RepairReturn::class);
    }

    public function repairPart()
    {
        return $this->belongsTo(RepairPart::class);
    }

    public function repairService()
    {
        return $this->belongsTo(RepairServiceItem::class, 'repair_service_id');
    }
}
