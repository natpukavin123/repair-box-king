<?php

namespace App\Http\Controllers;

use App\Models\{Repair, RepairReturn, RepairReturnItem, CreditNote, CreditNoteItem, LedgerTransaction, ActivityLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepairReturnController extends Controller
{
    public function create(Repair $repair)
    {
        // Check total IN payments cover the grand total (ignore refunds — they happen after returns)
        if ($repair->total_paid < $repair->grand_total) {
            abort(403, 'Returns can only be processed for fully paid repairs.');
        }

        $repair->load('customer', 'parts.part', 'repairServices.vendor', 'repairServices.serviceType', 'repairReturns.items');

        // Calculate already returned quantities
        $returnedParts = [];
        $returnedServices = [];
        foreach ($repair->repairReturns as $ret) {
            foreach ($ret->items as $item) {
                if ($item->item_type === 'part' && $item->repair_part_id) {
                    $returnedParts[$item->repair_part_id] = ($returnedParts[$item->repair_part_id] ?? 0) + $item->quantity;
                }
                if ($item->item_type === 'service' && $item->repair_service_id) {
                    $returnedServices[$item->repair_service_id] = true;
                }
            }
        }

        // Check if there are any returnable items left
        $hasReturnableParts = $repair->parts->contains(fn($rp) => $rp->quantity - ($returnedParts[$rp->id] ?? 0) > 0);
        $hasReturnableServices = $repair->repairServices->contains(fn($svc) => !isset($returnedServices[$svc->id]));
        $hasReturnableItems = $hasReturnableParts || $hasReturnableServices;

        return view('modules.repairs.returns.create', compact('repair', 'returnedParts', 'returnedServices', 'hasReturnableItems'));
    }

    public function store(Request $request, Repair $repair)
    {
        // Check total IN payments cover the grand total (ignore refunds)
        if ($repair->total_paid < $repair->grand_total) {
            return response()->json(['success' => false, 'message' => 'Returns can only be processed for fully paid repairs.'], 422);
        }

        $data = $request->validate([
            'reason' => 'required|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:part,service',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.return_amount' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($repair, $data) {
            $repair->load('parts.part', 'repairServices', 'repairReturns.items');

            // Calculate already returned quantities
            $returnedParts = [];
            $returnedServices = [];
            foreach ($repair->repairReturns as $ret) {
                foreach ($ret->items as $item) {
                    if ($item->item_type === 'part' && $item->repair_part_id) {
                        $returnedParts[$item->repair_part_id] = ($returnedParts[$item->repair_part_id] ?? 0) + $item->quantity;
                    }
                    if ($item->item_type === 'service' && $item->repair_service_id) {
                        $returnedServices[$item->repair_service_id] = true;
                    }
                }
            }

            $totalReturnAmount = 0;
            $returnItems = [];

            foreach ($data['items'] as $item) {
                if ($item['type'] === 'part') {
                    $rp = $repair->parts->find($item['id']);
                    if (!$rp)
                        continue;

                    $alreadyReturned = $returnedParts[$rp->id] ?? 0;
                    $maxQty = $rp->quantity - $alreadyReturned;
                    if ($item['quantity'] > $maxQty)
                        continue;

                    $returnItems[] = [
                        'item_type' => 'part',
                        'repair_part_id' => $rp->id,
                        'item_name' => $rp->part ? $rp->part->name : 'Part',
                        'quantity' => $item['quantity'],
                        'unit_price' => $rp->cost_price,
                        'return_amount' => $item['return_amount'],
                        'reason' => $item['reason'] ?? null,
                    ];
                    $totalReturnAmount += $item['return_amount'];
                } else {
                    $svc = $repair->repairServices->find($item['id']);
                    if (!$svc || isset($returnedServices[$svc->id]))
                        continue;

                    $returnItems[] = [
                        'item_type' => 'service',
                        'repair_service_id' => $svc->id,
                        'item_name' => $svc->service_type_name,
                        'quantity' => 1,
                        'unit_price' => $svc->customer_charge,
                        'return_amount' => $item['return_amount'],
                        'reason' => $item['reason'] ?? null,
                    ];
                    $totalReturnAmount += $item['return_amount'];
                }
            }

            if (empty($returnItems)) {
                throw new \Exception('No valid items to return.');
            }

            $return = RepairReturn::create([
                'return_number' => RepairReturn::generateReturnNumber(),
                'repair_id' => $repair->id,
                'customer_id' => $repair->customer_id,
                'reason' => $data['reason'],
                'total_return_amount' => $totalReturnAmount,
                'status' => 'confirmed',
                'created_by' => auth()->id(),
            ]);

            foreach ($returnItems as $ri) {
                $return->items()->create($ri);
            }

            // Auto-create Credit Note (approved status)
            $cn = CreditNote::create([
                'credit_note_number' => CreditNote::generateCreditNoteNumber(),
                'source_type' => 'repair',
                'source_id' => $repair->id,
                'customer_id' => $repair->customer_id,
                'total_amount' => $totalReturnAmount,
                'reason' => $data['reason'],
                'notes' => "Auto-created from return {$return->return_number}",
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'created_by' => auth()->id(),
            ]);

            // Create credit note items from return items
            foreach ($returnItems as $ri) {
                CreditNoteItem::create([
                    'credit_note_id' => $cn->id,
                    'item_type' => $ri['item_type'],
                    'item_name' => $ri['item_name'],
                    'quantity' => $ri['quantity'],
                    'unit_price' => $ri['unit_price'],
                    'total' => $ri['return_amount'],
                    'original_item_id' => $ri['repair_part_id'] ?? $ri['repair_service_id'] ?? null,
                ]);
            }

            // Link the credit note to the return
            $return->update(['credit_note_id' => $cn->id]);

            ActivityLog::log(
                'create',
                'repair_returns',
                $return->id,
                "Created return {$return->return_number} for repair {$repair->ticket_number} — ₹" . number_format($totalReturnAmount, 2)
            );

            ActivityLog::log(
                'create',
                'credit_notes',
                $cn->id,
                "Auto-created credit note {$cn->credit_note_number} from return {$return->return_number} — ₹" . number_format($totalReturnAmount, 2)
            );

            return response()->json([
                'success' => true,
                'message' => "Return {$return->return_number} created. Credit Note {$cn->credit_note_number} generated.",
                'redirect' => "/repairs/{$repair->id}/returns/{$return->id}",
            ]);
        });
    }

    public function show(Repair $repair, RepairReturn $return)
    {
        if ($return->repair_id !== $repair->id)
            abort(404);

        $return->load('items.repairPart.part', 'items.repairService', 'customer', 'creator', 'creditNote');
        $repair->load('customer', 'parts.part', 'repairServices', 'payments', 'repairReturns.items');

        return view('modules.repairs.returns.show', compact('repair', 'return'));
    }

    public function invoice(Repair $repair, RepairReturn $return)
    {
        if ($return->repair_id !== $repair->id)
            abort(404);

        $return->load('items.repairPart.part', 'items.repairService', 'customer', 'creator', 'creditNote');
        $repair->load('customer', 'parts.part', 'repairServices');

        return view('modules.repairs.returns.invoice', compact('repair', 'return'));
    }
}
