<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoRequest extends Model
{
    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_phone',
        'requested_items',
        'notes',
        'required_by',
        'status',
        'created_by',
    ];

    protected $casts = [
        'required_by' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
