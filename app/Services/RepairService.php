<?php

namespace App\Services;

use App\Models\{Repair, RepairStatusHistory, ActivityLog, LedgerTransaction};
use Illuminate\Support\Facades\DB;

class RepairService
{
    public function __construct(
        protected NotificationService $notifications = new NotificationService(),
    ) {}
    public function create(array $data): Repair
    {
        $repair = DB::transaction(function () use ($data) {
            $repair = Repair::create([
                'ticket_number' => Repair::generateTicketNumber(),
                'tracking_id' => Repair::generateTrackingId(),
                'customer_id' => $data['customer_id'] ?? null,
                'device_brand' => $data['device_brand'] ?? null,
                'device_model' => $data['device_model'] ?? null,
                'imei' => $data['imei'] ?? null,
                'problem_description' => $data['problem_description'] ?? null,
                'estimated_cost' => $data['estimated_cost'] ?? 0,
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => 'received',
            ]);

            RepairStatusHistory::create([
                'repair_id' => $repair->id,
                'status' => 'received',
                'notes' => 'Device received for repair',
                'updated_by' => auth()->id(),
            ]);

            // Record advance payment if provided during creation
            if (!empty($data['advance_amount']) && $data['advance_amount'] > 0) {
                $this->addPayment($repair, [
                    'payment_type' => 'advance',
                    'amount' => $data['advance_amount'],
                    'payment_method' => $data['advance_method'] ?? 'cash',
                    'reference_number' => $data['advance_reference'] ?? null,
                    'direction' => 'IN',
                ]);
            }

            ActivityLog::log('create', 'repairs', $repair->id, "Created repair {$repair->ticket_number}");

            return $repair->load('customer', 'payments');
        });

        // Fire received notification outside the transaction so a mail failure won't rollback
        try {
            $this->notifications->sendRepairReceived($repair);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[RepairService] Received notification failed: ' . $e->getMessage());
        }

        return $repair;
    }

    public function updateStatus(Repair $repair, string $status, ?string $notes = null, ?string $cancelReason = null): Repair
    {
        $updated = DB::transaction(function () use ($repair, $status, $notes, $cancelReason) {
            if ($repair->is_locked) {
                throw new \Exception('This repair is locked and cannot be modified.');
            }

            if (!$repair->canTransitionTo($status)) {
                throw new \Exception("Cannot change status from {$repair->status} to {$status}.");
            }

            $updateData = ['status' => $status];

            if ($status === 'completed') {
                $updateData['completed_at'] = now();
            }

            if ($status === 'payment') {
                // Moving to payment stage - ensure service charges are set
            }

            if ($status === 'closed') {
                $updateData['closed_at'] = now();
                $updateData['is_locked'] = true;
            }

            if ($status === 'cancelled') {
                $updateData['cancel_reason'] = $cancelReason;
            }

            $repair->update($updateData);

            RepairStatusHistory::create([
                'repair_id' => $repair->id,
                'status' => $status,
                'notes' => $notes,
                'updated_by' => auth()->id(),
            ]);

            ActivityLog::log('update', 'repairs', $repair->id, "Updated repair {$repair->ticket_number} to {$status}");

            return $repair->fresh('customer', 'statusHistory.updater', 'parts.part', 'payments');
        });

        // Fire completed notification outside the transaction so a mail failure won't rollback
        if ($status === 'completed') {
            try {
                $this->notifications->sendRepairCompleted($updated);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('[RepairService] Completed notification failed: ' . $e->getMessage());
            }
        }

        return $updated;
    }

    public function addPayment(Repair $repair, array $data): void
    {
        DB::transaction(function () use ($repair, $data) {
            $direction = $data['direction'] ?? 'IN';

            $repair->payments()->create([
                'payment_type' => $data['payment_type'],
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'direction' => $direction,
                'notes' => $data['notes'] ?? null,
            ]);

            $paymentLabel = $direction === 'OUT' ? 'refund' : 'payment';
            LedgerTransaction::create([
                'transaction_type' => 'repair',
                'reference_module' => 'repairs',
                'reference_id' => $repair->id,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'direction' => $direction,
                'description' => "Repair {$data['payment_type']} {$paymentLabel} - {$repair->ticket_number}",
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function cancelWithRefund(Repair $repair, string $reason, string $refundMethod = 'cash', string $partsAction = 'return_stock'): Repair
    {
        return DB::transaction(function () use ($repair, $reason, $refundMethod, $partsAction) {
            $repair->load('payments', 'parts.part');
            $netPaid = $repair->net_paid;

            // Process refund if there are advance payments
            if ($netPaid > 0) {
                $this->addPayment($repair, [
                    'payment_type' => 'refund',
                    'amount' => $netPaid,
                    'payment_method' => $refundMethod,
                    'direction' => 'OUT',
                    'notes' => "Refund on cancellation: {$reason}",
                ]);
            }

            // Handle parts
            if ($repair->parts->isNotEmpty()) {
                if ($partsAction === 'return_stock') {
                    // Return parts to inventory
                    foreach ($repair->parts as $repairPart) {
                            // Note: In this system, Parts are distinct from Inventory Products.
                            // We do not create StockMovements or update Inventory for Parts.
                    }
                    ActivityLog::log('update', 'repairs', $repair->id, "Parts returned to stock on cancellation");
                } else {
                    // Write off as loss — record as expense
                    $partsTotal = $repair->parts->sum(fn($p) => $p->cost_price * $p->quantity);

                    LedgerTransaction::create([
                        'transaction_type' => 'expense',
                        'reference_module' => 'repairs',
                        'reference_id' => $repair->id,
                        'amount' => $partsTotal,
                        'payment_method' => 'adjustment',
                        'direction' => 'OUT',
                        'description' => "Parts write-off on cancelled repair {$repair->ticket_number}",
                        'created_by' => auth()->id(),
                    ]);
                    ActivityLog::log('update', 'repairs', $repair->id, "Parts written off as loss (₹" . number_format($partsTotal, 2) . ")");
                }
            }

            return $this->updateStatus($repair->fresh(), 'cancelled', "Cancelled with refund of ₹{$netPaid}", $reason);
        });
    }

    public function createDuplicate(Repair $repair): Repair
    {
        return DB::transaction(function () use ($repair) {
            $dup = $repair->replicate(['status', 'is_locked', 'record_type', 'cancel_reason', 'completed_at', 'closed_at']);
            $dup->ticket_number = Repair::generateTicketNumber();
            $dup->tracking_id = Repair::generateTrackingId();
            $dup->parent_id = $repair->id;
            $dup->record_type = 'duplicate';
            $dup->status = 'received';
            $dup->service_charge = 0;
            $dup->save();

            RepairStatusHistory::create([
                'repair_id' => $dup->id,
                'status' => 'received',
                'notes' => "Duplicate of {$repair->ticket_number}",
                'updated_by' => auth()->id(),
            ]);

            ActivityLog::log('create', 'repairs', $dup->id, "Created duplicate of {$repair->ticket_number}");

            return $dup->load('customer');
        });
    }
}
