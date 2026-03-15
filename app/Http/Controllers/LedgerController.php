<?php

namespace App\Http\Controllers;

use App\Models\LedgerTransaction;

class LedgerController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = LedgerTransaction::with('creator')
                ->when(request('search'), fn($q, $s) => $q->where('description', 'like', "%{$s}%"))
                ->when(request('transaction_type'), fn($q, $t) => $q->where('transaction_type', $t))
                ->when(request('direction'), fn($q, $d) => $q->where('direction', $d))
                ->when(request('date_from'), fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when(request('date_to'), fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->latest()
                ->paginate(request('per_page', 20));
            return response()->json($data);
        }
        return view('modules.ledger.index');
    }

    public function summary()
    {
        $totalIn = LedgerTransaction::where('direction', 'IN')->sum('amount');
        $totalOut = LedgerTransaction::where('direction', 'OUT')->sum('amount');
        $byType = LedgerTransaction::select('transaction_type')
            ->selectRaw('SUM(CASE WHEN direction = "IN" THEN amount ELSE 0 END) as total_in')
            ->selectRaw('SUM(CASE WHEN direction = "OUT" THEN amount ELSE 0 END) as total_out')
            ->groupBy('transaction_type')
            ->get();

        return response()->json([
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'balance' => $totalIn - $totalOut,
            'by_type' => $byType,
        ]);
    }
}
