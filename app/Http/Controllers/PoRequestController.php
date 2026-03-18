<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PoRequest;
use Illuminate\Http\Request;

class PoRequestController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = PoRequest::with('customer', 'creator')
                ->when(request('status'), fn($q, $status) => $q->where('status', $status))
                ->when(request('search'), function ($q, $search) {
                    $q->where(function ($inner) use ($search) {
                        $inner->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('customer_phone', 'like', "%{$search}%")
                            ->orWhere('requested_items', 'like', "%{$search}%")
                            ->orWhereHas('customer', fn($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('mobile_number', 'like', "%{$search}%"));
                    });
                })
                ->when(request('date_from'), fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when(request('date_to'), fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->latest();

            // Return status counts alongside paginated data
            if (request()->boolean('with_counts')) {
                $baseQuery = (clone $query)->getQuery();
                // Remove status constraint for counts
                $countQuery = PoRequest::query()
                    ->when(request('search'), function ($q, $search) {
                        $q->where(function ($inner) use ($search) {
                            $inner->where('customer_name', 'like', "%{$search}%")
                                ->orWhere('customer_phone', 'like', "%{$search}%")
                                ->orWhere('requested_items', 'like', "%{$search}%")
                                ->orWhereHas('customer', fn($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('mobile_number', 'like', "%{$search}%"));
                        });
                    })
                    ->when(request('date_from'), fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                    ->when(request('date_to'), fn($q, $d) => $q->whereDate('created_at', '<=', $d));

                $counts = (clone $countQuery)->selectRaw('status, COUNT(*) as cnt')->groupBy('status')->pluck('cnt', 'status');
                $allTotal = (clone $countQuery)->count();

                $paginated = $query->paginate(request('per_page', 15));
                $response = $paginated->toArray();
                $response['counts'] = [
                    'all'       => $allTotal,
                    'open'      => $counts['open'] ?? 0,
                    'ordered'   => $counts['ordered'] ?? 0,
                    'received'  => $counts['received'] ?? 0,
                    'completed' => $counts['completed'] ?? 0,
                    'cancelled' => $counts['cancelled'] ?? 0,
                ];
                return response()->json($response);
            }

            return response()->json($query->paginate(request('per_page', 15)));
        }

        return view('modules.po.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'requested_items' => 'required|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'required_by' => 'nullable|date',
        ]);

        // Require at least a customer_id or customer_name
        if (empty($data['customer_id']) && empty($data['customer_name'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a customer name or select an existing customer.',
            ], 422);
        }

        // Auto-fill name/phone from selected customer
        if (!empty($data['customer_id'])) {
            $customer = Customer::find($data['customer_id']);
            if ($customer) {
                $data['customer_name'] = $data['customer_name'] ?: $customer->name;
                $data['customer_phone'] = $data['customer_phone'] ?: $customer->mobile_number;
            }
        }

        $data['created_by'] = auth()->id();
        $data['status'] = 'open';

        $poRequest = PoRequest::create($data)->load('customer', 'creator');

        return response()->json([
            'success' => true,
            'data' => $poRequest,
            'message' => 'PO request saved successfully.',
        ]);
    }

    public function updateStatus(Request $request, PoRequest $poRequest)
    {
        $status = $request->validate([
            'status' => 'required|in:open,ordered,received,completed,cancelled',
        ])['status'];

        $poRequest->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => 'PO request status updated.',
        ]);
    }
}
