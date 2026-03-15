<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairReturn extends Model
{
    protected $fillable = [
        'return_number', 'repair_id', 'customer_id', 'reason',
        'total_return_amount', 'refund_amount', 'refund_method',
        'refund_reference', 'refund_notes', 'status', 'refunded_at', 'created_by',
        'credit_note_id',
    ];

    protected $casts = [
        'total_return_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'refunded_at' => 'datetime',
    ];

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(RepairReturnItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public static function generateReturnNumber(): string
    {
        $last = self::orderBy('id', 'desc')->first();
        $number = $last ? intval(substr($last->return_number, 4)) + 1 : 1;
        return 'RTN-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
