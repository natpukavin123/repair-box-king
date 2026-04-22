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
            $query = Repair::with('customer', 'payments')
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

            if (request('view') === 'kanban') {
                $data = $query->latest()->get();
                $data->transform(function ($repair) {
                    $repair->is_fully_paid = $repair->is_fully_paid;
                    $repair->grand_total   = $repair->grand_total;
                    $repair->total_paid    = $repair->total_paid;
                    $repair->net_paid      = $repair->net_paid;
                    $repair->balance_due   = $repair->balance_due;
                    return $repair;
                });
                return response()->json(['data' => $data]);
            }

            $data = $query->latest()->paginate(request('per_page', 15));
            $data->getCollection()->transform(function ($repair) {
                $repair->is_fully_paid = $repair->is_fully_paid;
                $repair->grand_total   = $repair->grand_total;
                $repair->total_paid    = $repair->total_paid;
                $repair->net_paid      = $repair->net_paid;
                $repair->balance_due   = $repair->balance_due;
                return $repair;
            });
            return response()->json($data);
        }

        $statusMeta = Repair::STATUS_META;
        $brandModelMap = \App\Models\Brand::where('status', 'active')->orderBy('name')->get(['name', 'models'])
            ->map(fn($b) => ['name' => $b->name, 'models' => $b->models ?? []])->values();
        $brands = $brandModelMap->pluck('name');

        // Gather unique issues from past repairs for auto-suggest
        $dbProblemSuggestions = Repair::whereNotNull('problem_description')
            ->where('problem_description', '!=', '')
            ->pluck('problem_description')
            ->flatMap(fn($desc) => array_map('trim', explode(',', $desc)))
            ->filter(fn($s) => strlen($s) > 1)
            ->unique()
            ->values()
            ->toArray();

        return view('modules.repairs.index', compact('statusMeta', 'brands', 'brandModelMap', 'dbProblemSuggestions'));
    }

    public function create()
    {
        $brandModelMap = \App\Models\Brand::where('status', 'active')->orderBy('name')->get(['name', 'models'])
            ->map(fn($b) => ['name' => $b->name, 'models' => $b->models ?? []])->values();
        $brands = $brandModelMap->pluck('name');
        return view('modules.repairs.create', compact('brands', 'brandModelMap'));
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
        $repair->load('customer', 'statusHistory.updater', 'payments', 'childRepairs');
        $repair->is_fully_paid   = $repair->is_fully_paid;
        $repair->grand_total     = $repair->grand_total;
        $repair->total_paid      = $repair->total_paid;
        $repair->net_paid        = $repair->net_paid;
        $repair->balance_due     = $repair->balance_due;
        $repair->total_refunded  = $repair->total_refunded;
        $repair->allowed_transitions = Repair::STATUS_TRANSITIONS[$repair->status] ?? [];
        $repair->status_meta     = Repair::STATUS_META;

        if (request()->ajax()) {
            return response()->json($repair);
        }

        $statusMeta   = Repair::STATUS_META;
        $brandModelMap = \App\Models\Brand::where('status', 'active')->orderBy('name')->get(['name', 'models'])
            ->map(fn($b) => ['name' => $b->name, 'models' => $b->models ?? []])->values();
        $brands = $brandModelMap->pluck('name');
        return view('modules.repairs.show', compact('repair', 'statusMeta', 'brands', 'brandModelMap'));
    }

    public function update(RepairRequest $request, Repair $repair)
    {
        if ($repair->is_locked) {
            return response()->json(['success' => false, 'message' => 'This repair is locked and cannot be edited.'], 422);
        }

        $repair->update($request->validated());

        $repair->load('customer', 'statusHistory.updater', 'payments', 'childRepairs');

        return response()->json(['success' => true, 'data' => $repair, 'message' => 'Repair intake details updated']);
    }

    public function updateStatus(Request $request, Repair $repair, RepairService $service)
    {
        $data = $request->validate([
            'status'        => 'required|string|in:' . implode(',', Repair::STATUSES),
            'notes'         => 'nullable|string|max:500',
            'cancel_reason' => 'nullable|string|max:500',
        ]);

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
            'payment_type'     => 'required|in:advance,final,refund',
            'amount'           => 'required|numeric|min:0.01',
            'payment_method'   => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'direction'        => 'nullable|in:IN,OUT',
            'notes'            => 'nullable|string|max:500',
        ]);

        $data['direction'] = $data['direction'] ?? ($data['payment_type'] === 'refund' ? 'OUT' : 'IN');

        $service->addPayment($repair, $data);

        return response()->json(['success' => true, 'message' => 'Payment recorded']);
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

    public function duplicateRepair(Repair $repair, RepairService $service)
    {
        try {
            $dup = $service->createDuplicate($repair);
            return response()->json(['success' => true, 'data' => $dup, 'message' => 'Duplicate created: ' . $dup->ticket_number]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function trackingLanding()
    {
        $shopName     = \App\Models\Setting::getValue('shop_name', 'RepairBox');
        $shopPhone    = \App\Models\Setting::getValue('shop_phone', '');
        $shopEmail    = \App\Models\Setting::getValue('shop_email', '');
        $shopSlogan   = \App\Models\Setting::getValue('shop_slogan', 'Your Trusted Mobile Partner');
        $shopIcon     = \App\Models\Setting::getValue('shop_icon', '');
        $shopFavicon  = \App\Models\Setting::getValue('shop_favicon', '');
        $shopWhatsapp = \App\Models\Setting::getValue('shop_whatsapp', '');
        $shopAddress  = \App\Models\Setting::getValue('shop_address', '');
        return view('public.track', compact('shopName', 'shopPhone', 'shopEmail', 'shopSlogan', 'shopIcon', 'shopFavicon', 'shopWhatsapp', 'shopAddress'));
    }

    public function track($trackingId)
    {
        if (request()->wantsJson()) {
            $repair = Repair::with('statusHistory', 'customer')
                ->where('tracking_id', $trackingId)
                ->firstOrFail();
            return response()->json($repair);
        }

        $repair   = Repair::with(['statusHistory', 'customer', 'payments'])
            ->where('tracking_id', $trackingId)
            ->first();
        $notFound = $repair === null;

        $shopName     = \App\Models\Setting::getValue('shop_name', 'RepairBox');
        $shopPhone    = \App\Models\Setting::getValue('shop_phone', '');
        $shopEmail    = \App\Models\Setting::getValue('shop_email', '');
        $shopSlogan   = \App\Models\Setting::getValue('shop_slogan', 'Your Trusted Mobile Partner');
        $shopIcon     = \App\Models\Setting::getValue('shop_icon', '');
        $shopFavicon  = \App\Models\Setting::getValue('shop_favicon', '');
        $shopWhatsapp = \App\Models\Setting::getValue('shop_whatsapp', '');
        $shopAddress  = \App\Models\Setting::getValue('shop_address', '');

        return view('public.track', compact('repair', 'notFound', 'shopName', 'shopPhone', 'shopEmail', 'shopSlogan', 'shopIcon', 'shopFavicon', 'shopWhatsapp', 'shopAddress'));
    }

    public function print(Repair $repair)
    {
        $repair->load('customer', 'payments', 'statusHistory');
        return view('modules.repairs.print', compact('repair'));
    }

    public function invoice(Repair $repair)
    {
        $repair->load('customer', 'payments');
        return view('modules.repairs.invoice', compact('repair'));
    }
}
