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
                ->when(request('order_type'), fn($q, $type) => $q->where('order_type', $type))
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
                    ->when(request('order_type'), fn($q, $type) => $q->where('order_type', $type))
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
            'order_type' => 'required|in:customer,store',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'requested_items' => 'required|array|min:1',
            'requested_items.*.name' => 'required|string|max:500',
            'requested_items.*.qty' => 'required|integer|min:1',
            'requested_items.*.source' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:2000',
            'required_by' => 'nullable|date',
        ]);

        // Require customer for customer-type orders
        if ($data['order_type'] === 'customer' && empty($data['customer_id']) && empty($data['customer_name'])) {
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

        // Ensure each item has a 'done' flag
        $data['requested_items'] = array_map(function ($item) {
            return [
                'name' => $item['name'],
                'qty' => $item['qty'] ?? 1,
                'source' => $item['source'] ?? 'custom',
                'done' => false,
            ];
        }, $data['requested_items']);

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
            'data' => $poRequest->fresh()->load('customer', 'creator'),
            'message' => 'PO request status updated.',
        ]);
    }

    public function toggleItem(Request $request, PoRequest $poRequest)
    {
        $index = $request->validate(['index' => 'required|integer|min:0'])['index'];
        $items = $poRequest->requested_items;

        if (!isset($items[$index])) {
            return response()->json(['success' => false, 'message' => 'Item not found.'], 404);
        }

        $items[$index]['done'] = !($items[$index]['done'] ?? false);
        $poRequest->update(['requested_items' => $items]);

        return response()->json([
            'success' => true,
            'data' => $poRequest->fresh()->load('customer', 'creator'),
            'message' => 'Item updated.',
        ]);
    }
}
