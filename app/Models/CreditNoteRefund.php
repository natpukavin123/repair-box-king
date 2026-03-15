<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNoteRefund extends Model
{
    protected $fillable = [
        'credit_note_id', 'resolution_type', 'reference_type', 'reference_id',
        'amount', 'method', 'reference_number',
        'notes', 'processed_by', 'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
