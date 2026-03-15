<?php

namespace App\Http\Controllers;

use App\Models\Recharge;
use App\Models\LedgerTransaction;
use App\Http\Requests\RechargeRequest;

class RechargeController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = Recharge::with('customer', 'provider')
                ->when(request('search'), fn($q, $s) => $q->where('mobile_number', 'like', "%{$s}%")->orWhere('transaction_id', 'like', "%{$s}%"))
                ->when(request('customer_id'), fn($q, $id) => $q->where('customer_id', $id))
                ->when(request('provider_id'), fn($q, $id) => $q->where('provider_id', $id))
                ->when(request('status'), fn($q, $s) => $q->where('status', $s))
                ->when(request('date_from'), fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when(request('date_to'), fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->latest()
                ->paginate(request('per_page', 15));
            return response()->json($data);
        }
        return view('modules.recharges.index');
    }

    public function store(RechargeRequest $request)
    {
        $data = $request->validated();
        $data['status'] = 'success';
        $recharge = Recharge::create($data);

        LedgerTransaction::create([
            'transaction_type' => 'recharge',
            'reference_module' => 'recharges',
            'reference_id' => $recharge->id,
            'amount' => $recharge->recharge_amount,
            'payment_method' => $recharge->payment_method,
            'direction' => 'IN',
            'description' => "Recharge {$recharge->mobile_number}",
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $recharge->load('provider', 'customer'), 'message' => 'Recharge completed']);
    }

    public function show(Recharge $recharge)
    {
        return response()->json($recharge->load('customer', 'provider'));
    }
}
