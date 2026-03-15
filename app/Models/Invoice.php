<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_id', 'total_amount', 'discount',
        'tax_amount', 'cgst_amount', 'sgst_amount', 'igst_amount', 'is_igst',
        'final_amount', 'payment_status', 'is_locked', 'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'is_locked' => 'boolean',
        'is_igst' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function creditNotes()
    {
        return $this->hasMany(CreditNote::class, 'source_id')->where('source_type', 'invoice');
    }

    public function isLocked(): bool
    {
        return (bool) $this->is_locked;
    }

    public function lock(): void
    {
        $this->update(['is_locked' => true]);
    }

    public static function generateInvoiceNumber(): string
    {
        $last = self::orderBy('id', 'desc')->first();
        $number = $last ? intval(substr($last->invoice_number, 4)) + 1 : 1;
        return 'INV-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
