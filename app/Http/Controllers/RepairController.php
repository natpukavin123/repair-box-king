<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\RepairStatusHistory;
use App\Http\Requests\RepairRequest;
use App\Services\RepairService;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = Repair::with('customer', 'parts.part', 'payments', 'repairServices')
                ->when(request('search'), function ($q, $s) {
                    $q->where(function ($q2) use ($s) {
                        $q2->where('ticket_number', 'like', "%{$s}%")
                           ->orWhere('tracking_id', 'like', "%{$s}%")
                           ->orWhere('device_brand', 'like', "%{$s}%")
                           ->orWhere('device_model', 'like', "%{$s}%")
                           ->orWhere('imei', 'like', "%{$s}%")
                           ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$s}%")->orWhere('mobile_number', 'like', "%{$s}%"));
                    });
                })
                ->when(request('status'), fn($q, $s) => $q->where('status', $s))
                ->when(request('date_from'), fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when(request('date_to'), fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->when(request('payment_status'), function($q, $ps) {
                    if ($ps === 'paid') $q->whereHas('payments');
                    if ($ps === 'unpaid') $q->whereDoesntHave('payments');
                })
                ->when(request('record_type'), fn($q, $t) => $q->where('record_type', $t), fn($q) => $q->where('record_type', 'original'));

            // For kanban view, load all without pagination
            if (request('view') === 'kanban') {
                $data = $query->latest()->get();
                $data->transform(function ($repair) {
                    $repair->is_fully_paid = $repair->is_fully_paid;
                    $repair->grand_total = $repair->grand_total;
                    $repair->total_paid = $repair->total_paid;
                    $repair->net_paid = $repair->net_paid;
                    $repair->balance_due = $repair->balance_due;
                    $repair->total_refunded = $repair->total_refunded;
                    $repair->total_parts = $repair->total_parts;
                    $repair->total_services = $repair->total_services;
                    $repair->parts_cost = $repair->parts_cost;
                    $repair->vendor_charges = $repair->vendor_charges;
                    $repair->total_cost = $repair->total_cost;
                    $repair->profit = $repair->profit;
                    return $repair;
                });
                return response()->json(['data' => $data]);
            }

            $data = $query->latest()->paginate(request('per_page', 15));
            $data->getCollection()->transform(function ($repair) {
                $repair->is_fully_paid = $repair->is_fully_paid;
                $repair->grand_total = $repair->grand_total;
                $repair->total_paid = $repair->total_paid;
                $repair->net_paid = $repair->net_paid;
                $repair->balance_due = $repair->balance_due;
                $repair->total_refunded = $repair->total_refunded;
                $repair->total_parts = $repair->total_parts;
                $repair->total_services = $repair->total_services;
                $repair->parts_cost = $repair->parts_cost;
                $repair->vendor_charges = $repair->vendor_charges;
                $repair->total_cost = $repair->total_cost;
                $repair->profit = $repair->profit;
                return $repair;
            });
            return response()->json($data);
        }

        $statusMeta = Repair::STATUS_META;
        return view('modules.repairs.index', compact('statusMeta'));
    }

    public function create()
    {
        $brands = \App\Models\Brand::where('status', 'active')->orderBy('name')->pluck('name');
        return view('modules.repairs.create', compact('brands'));
    }

    public function store(RepairRequest $request, RepairService $service)
    {
        $data = $request->validated();
        // Include advance payment fields
        $data['advance_amount'] = $request->input('advance_amount');
        $data['advance_method'] = $request->input('advance_method', 'cash');
        $data['advance_reference'] = $request->input('advance_reference');

        $repair = $service->create($data);
        return response()->json(['success' => true, 'data' => $repair, 'message' => 'Repair ticket created']);
    }

    public function show(Repair $repair)
    {
        $repair->load('customer', 'statusHistory.updater', 'parts.part', 'payments', 'repairVendors.vendor', 'repairServices.vendor', 'repairServices.serviceType', 'childRepairs', 'repairReturns.items');
        $repair->is_fully_paid = $repair->is_fully_paid;
        $repair->grand_total = $repair->grand_total;
        $repair->total_paid = $repair->total_paid;
        $repair->net_paid = $repair->net_paid;
        $repair->balance_due = $repair->balance_due;
        $repair->total_refunded = $repair->total_refunded;
        $repair->total_services = $repair->total_services;
        $repair->allowed_transitions = Repair::STATUS_TRANSITIONS[$repair->status] ?? [];
        $repair->status_meta = Repair::STATUS_META;

        // Compute return status for the repair
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
        $hasReturnableParts = $repair->parts->contains(fn($rp) => $rp->quantity - ($returnedParts[$rp->id] ?? 0) > 0);
        $hasReturnableServices = $repair->repairServices->contains(fn($svc) => !isset($returnedServices[$svc->id]));
        $repair->has_returnable_items = $hasReturnableParts || $hasReturnableServices;

        if ($repair->repairReturns->count() === 0) {
            $repair->return_status = 'none';
        } elseif ($repair->has_returnable_items) {
            $repair->return_status = 'partial';
        } else {
            $repair->return_status = 'fully_returned';
        }

        if (request()->ajax()) {
            return response()->json($repair);
        }

        $statusMeta = Repair::STATUS_META;
        $brands = \App\Models\Brand::where('status', 'active')->orderBy('name')->pluck('name');
        return view('modules.repairs.show', compact('repair', 'statusMeta', 'brands'));
    }

    public function update(RepairRequest $request, Repair $repair)
    {
        if ($repair->is_locked) {
            return response()->json(['success' => false, 'message' => 'This repair is locked and cannot be edited.'], 422);
        }

        $repair->update($request->validated());
        $repair->load('customer', 'statusHistory.updater', 'parts.part', 'payments', 'repairVendors.vendor', 'repairServices.vendor', 'repairServices.serviceType', 'childRepairs', 'repairReturns.items');

        return response()->json(['success' => true, 'data' => $repair, 'message' => 'Repair intake details updated']);
    }

    public function updateStatus(Request $request, Repair $repair, RepairService $service)
    {
        $data = $request->validate([
            'status' => 'required|string|in:' . implode(',', Repair::STATUSES),
            'notes' => 'nullable|string|max:500',
            'cancel_reason' => 'nullable|string|max:500',
            'confirm' => 'nullable|boolean',
        ]);

        // Completed status requires confirmation
        if ($data['status'] === 'completed' && empty($data['confirm'])) {
            return response()->json(['success' => false, 'message' => 'Please confirm to mark as completed.'], 422);
        }

        try {
            $repair = $service->updateStatus(
                $repair,
                $data['status'],
                $data['notes'] ?? null,
                $data['cancel_reason'] ?? null
            );
            return response()->json(['success' => true, 'data' => $repair, 'message' => 'Status updated']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function addPayment(Request $request, Repair $repair, RepairService $service)
    {
        $data = $request->validate([
            'payment_type' => 'required|in:advance,final,refund',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'direction' => 'nullable|in:IN,OUT',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($repair->is_locked) {
            return response()->json(['success' => false, 'message' => 'This repair is locked.'], 422);
        }

        $data['direction'] = $data['direction'] ?? ($data['payment_type'] === 'refund' ? 'OUT' : 'IN');

        $service->addPayment($repair, $data);

        // Auto-close repair if fully paid
        $repair->refresh();
        if ($repair->status === 'payment' && $repair->is_fully_paid) {
            try {
                $service->updateStatus($repair, 'closed', 'Auto-closed upon full payment');
            } catch (\Exception $e) {
                // Ignore transition errors if it somehow fails, payment was still recorded
            }
        }

        return response()->json(['success' => true, 'message' => 'Payment recorded']);
    }

    public function addPart(Request $request, Repair $repair)
    {
        $data = $request->validate([
            'part_id' => 'required|exists:parts,id',
            'quantity' => 'required|integer|min:1',
            'cost_price' => 'required|numeric|min:0',
        ]);

        if ($repair->is_locked) {
            return response()->json(['success' => false, 'message' => 'This repair is locked.'], 422);
        }

        if ($repair->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Parts can only be added during in-progress status.'], 422);
        }

        // Auto-populate from Part master
        $partModel = \App\Models\Part::find($data['part_id']);

        $part = $repair->parts()->create($data);

        // Log to status history so it appears in Status & History tab
        $partName = $partModel?->name ?? 'Part';
        RepairStatusHistory::create([
            'repair_id'  => $repair->id,
            'status'     => $repair->status,
            'notes'      => "Part added: {$partName} × {$data['quantity']} @ ₹" . number_format($data['cost_price'], 2),
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Part added']);
    }

    public function removePart(Repair $repair, $partId)
    {
        if ($repair->is_locked) {
            return response()->json(['success' => false, 'message' => 'This repair is locked.'], 422);
        }

        $repairPart = $repair->parts()->with('part')->where('id', $partId)->first();
        $partName = $repairPart?->part?->name ?? 'Part';

        $repair->parts()->where('id', $partId)->delete();

        RepairStatusHistory::create([
            'repair_id'  => $repair->id,
            'status'     => $repair->status,
            'notes'      => "Part removed: {$partName}",
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Part removed']);
    }

    public function addService(Request $request, Repair $repair)
    {
        $data = $request->validate([
            'service_type_id' => 'nullable|exists:service_types,id',
            'service_type_name' => 'required|string|max:150',
            'vendor_id' => 'nullable|exists:vendors,id',
            'customer_charge' => 'required|numeric|min:0',
            'vendor_charge' => 'nullable|numeric|min:0',
            'reference_no' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($repair->is_locked) {
            return response()->json(['success' => false, 'message' => 'This repair is locked.'], 422);
        }

        if (!in_array($repair->status, ['in_progress', 'completed', 'payment'])) {
            return response()->json(['success' => false, 'message' => 'Services can only be added during in-progress, completed, or payment status.'], 422);
        }

        $data['vendor_charge'] = $data['vendor_charge'] ?? 0;

        $repair->repairServices()->create($data);

        // Log to status history
        RepairStatusHistory::create([
            'repair_id'  => $repair->id,
            'status'     => $repair->status,
            'notes'      => "Service added: {$data['service_type_name']}, ₹" . number_format($data['customer_charge'], 2),
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Service added']);
    }

    public function updateService(Request $request, Repair $repair, $serviceId)
    {
        $data = $request->validate([
            'vendor_charge'   => 'nullable|numeric|min:0',
            'customer_charge' => 'nullable|numeric|min:0',
            'reference_no'    => 'nullable|string|max:100',
            'description'     => 'nullable|string|max:1000',
        ]);

        if ($repair->is_locked) {
            return response()->json(['success' => false, 'message' => 'This repair is locked.'], 422);
        }

        $repair->repairServices()->where('id', $serviceId)->update($data);
        return response()->json(['success' => true, 'message' => 'Service updated']);
    }

    public function removeService(Repair $repair, $serviceId)
    {
        if ($repair->is_locked) {
            return response()->json(['success' => false, 'message' => 'This repair is locked.'], 422);
        }

        $svc = $repair->repairServices()->where('id', $serviceId)->first();
        $svcName = $svc?->service_type_name ?? 'Service';

        $repair->repairServices()->where('id', $serviceId)->delete();

        RepairStatusHistory::create([
            'repair_id'  => $repair->id,
            'status'     => $repair->status,
            'notes'      => "Service removed: {$svcName}",
            'updated_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Service removed']);
    }

    public function updateServiceCharge(Request $request, Repair $repair)
    {
        $data = $request->validate([
            'service_charge' => 'required|numeric|min:0',
        ]);

        if ($repair->is_locked) {
            return response()->json(['success' => false, 'message' => 'This repair is locked.'], 422);
        }

        if (!in_array($repair->status, ['in_progress', 'completed', 'payment'])) {
            return response()->json(['success' => false, 'message' => 'Service charge can only be set when repair is in progress, completed, or in payment stage.'], 422);
        }

        $repair->update($data);
        return response()->json(['success' => true, 'data' => $repair->fresh(), 'message' => 'Service charge updated']);
    }

    public function cancel(Request $request, Repair $repair, RepairService $service)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $repair = $service->updateStatus($repair, 'cancelled', 'Repair cancelled', $data['reason']);
            return response()->json(['success' => true, 'data' => $repair, 'message' => 'Repair cancelled']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function cancelWithRefund(Request $request, Repair $repair, RepairService $service)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
            'refund_method' => 'required|string|max:50',
            'parts_action' => 'nullable|string|in:return_stock,write_off',
        ]);

        try {
            $repair = $service->cancelWithRefund($repair, $data['reason'], $data['refund_method'], $data['parts_action'] ?? 'return_stock');
            return response()->json(['success' => true, 'data' => $repair, 'message' => 'Repair cancelled with refund processed']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function duplicateRepair(Repair $repair, RepairService $service)
    {
        try {
            $dup = $service->createDuplicate($repair);
            return response()->json(['success' => true, 'data' => $dup, 'message' => 'Duplicate created: ' . $dup->ticket_number]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function track($trackingId)
    {
        $repair = Repair::with('statusHistory', 'customer')
            ->where('tracking_id', $trackingId)
            ->firstOrFail();
        return response()->json($repair);
    }

    public function print(Repair $repair)
    {
        $repair->load('customer', 'parts.part', 'payments', 'repairServices', 'statusHistory');
        return view('modules.repairs.print', compact('repair'));
    }

    public function invoice(Repair $repair)
    {
        $repair->load('customer', 'parts.part', 'payments', 'repairServices', 'repairReturns.items');
        return view('modules.repairs.invoice', compact('repair'));
    }

    public function costBreakdown(Repair $repair)
    {
        $repair->load('customer', 'parts.part', 'payments', 'repairServices.vendor', 'repairServices.serviceType');
        return view('modules.repairs.cost-breakdown', compact('repair'));
    }
}
