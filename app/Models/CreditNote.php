<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $fillable = [
        'credit_note_number', 'source_type', 'source_id', 'customer_id',
        'total_amount', 'refunded_amount', 'reason', 'notes', 'status',
        'approved_by', 'approved_at', 'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(CreditNoteItem::class);
    }

    public function refunds()
    {
        return $this->hasMany(CreditNoteRefund::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the source invoice (if source_type = 'invoice').
     */
    public function sourceInvoice()
    {
        return $this->belongsTo(Invoice::class, 'source_id');
    }

    /**
     * Get the source repair (if source_type = 'repair').
     */
    public function sourceRepair()
    {
        return $this->belongsTo(Repair::class, 'source_id');
    }

    /**
     * Get the source model (Invoice or Repair).
     */
    public function getSourceAttribute()
    {
        if ($this->source_type === 'invoice') {
            return $this->sourceInvoice;
        }
        return $this->sourceRepair;
    }

    /**
     * Remaining refundable amount.
     */
    public function remainingRefundable(): float
    {
        return round($this->total_amount - $this->refunded_amount, 2);
    }

    public function isFullyRefunded(): bool
    {
        return $this->refunded_amount >= $this->total_amount;
    }

    public static function generateCreditNoteNumber(): string
    {
        $last = self::orderBy('id', 'desc')->first();
        $number = $last ? intval(substr($last->credit_note_number, 3)) + 1 : 1;
        return 'CN-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
