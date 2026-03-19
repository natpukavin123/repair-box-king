<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    // Status flow: received → in_progress → completed → payment → closed
    // Side statuses: cancelled
    const STATUS_RECEIVED = 'received';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PAYMENT = 'payment';
    const STATUS_CLOSED = 'closed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_RECEIVED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
        self::STATUS_PAYMENT,
        self::STATUS_CLOSED,
        self::STATUS_CANCELLED,
    ];

    // Which statuses can transition to which
    const STATUS_TRANSITIONS = [
        'received' => ['in_progress', 'cancelled'],
        'in_progress' => ['completed', 'cancelled'],
        'completed' => ['payment', 'cancelled'],
        'payment' => ['closed', 'cancelled'],
        'closed' => [],
        'cancelled' => [],
    ];

    // Labels and colors for UI
    const STATUS_META = [
        'received'    => ['label' => 'Received',    'color' => 'blue',   'icon' => 'inbox'],
        'in_progress' => ['label' => 'In Progress', 'color' => 'amber',  'icon' => 'wrench'],
        'completed'   => ['label' => 'Completed',   'color' => 'emerald','icon' => 'check-circle'],
        'payment'     => ['label' => 'Payment',     'color' => 'purple', 'icon' => 'currency'],
        'closed'      => ['label' => 'Closed',      'color' => 'green',  'icon' => 'lock'],
        'cancelled'   => ['label' => 'Cancelled',   'color' => 'red',    'icon' => 'x-circle'],
    ];

    protected $fillable = [
        'ticket_number', 'tracking_id', 'customer_id', 'device_brand',
        'device_model', 'imei', 'problem_description', 'estimated_cost',
        'service_charge', 'expected_delivery_date', 'status',
        'is_locked', 'parent_id', 'record_type', 'cancel_reason',
        'completed_at', 'closed_at',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'expected_delivery_date' => 'date',
        'is_locked' => 'boolean',
        'completed_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(RepairStatusHistory::class);
    }

    public function parts()
    {
        return $this->hasMany(RepairPart::class);
    }

    public function payments()
    {
        return $this->hasMany(RepairPayment::class);
    }

    public function repairVendors()
    {
        return $this->hasMany(RepairVendor::class);
    }

    public function repairServices()
    {
        return $this->hasMany(RepairServiceItem::class);
    }

    public function repairReturns()
    {
        return $this->hasMany(RepairReturn::class);
    }

    public function parentRepair()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function childRepairs()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public static function generateTicketNumber(): string
    {
        $last = self::orderBy('id', 'desc')->first();
        $number = $last ? intval(substr($last->ticket_number, 4)) + 1 : 1;
        return 'RPR-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public static function generateTrackingId(): string
    {
        return 'TRK-' . strtoupper(bin2hex(random_bytes(4)));
    }

    public function canTransitionTo(string $status): bool
    {
        $allowed = self::STATUS_TRANSITIONS[$this->status] ?? [];
        return in_array($status, $allowed);
    }

    public function getTotalPartsAttribute(): float
    {
        return $this->parts->sum(fn($p) => $p->cost_price * $p->quantity);
    }

    public function getTotalServicesAttribute(): float
    {
        return $this->repairServices->sum('customer_charge');
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments->where('direction', 'IN')->sum('amount');
    }

    public function getTotalRefundedAttribute(): float
    {
        return $this->payments->where('direction', 'OUT')->sum('amount');
    }

    public function getNetPaidAttribute(): float
    {
        return $this->total_paid - $this->total_refunded;
    }

    public function getGrandTotalAttribute(): float
    {
        return $this->total_parts + (float) $this->service_charge + $this->total_services;
    }

    public function getBalanceDueAttribute(): float
    {
        return max(0, $this->grand_total - $this->total_paid);
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->grand_total > 0 && $this->net_paid >= $this->grand_total;
    }

    public function getPartsCostAttribute(): float
    {
        return $this->parts->sum(function ($rp) {
            $actualCost = $rp->part ? $rp->part->cost_price : $rp->cost_price;
            return (float) $actualCost * $rp->quantity;
        });
    }

    public function getVendorChargesAttribute(): float
    {
        return $this->repairServices->sum('vendor_charge');
    }

    public function getTotalCostAttribute(): float
    {
        return $this->parts_cost + $this->vendor_charges;
    }

    public function getProfitAttribute(): float
    {
        return $this->grand_total - $this->total_cost;
    }
}
