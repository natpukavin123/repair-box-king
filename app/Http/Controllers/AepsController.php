<?php

namespace App\Http\Controllers;

use App\Models\AepsWallet;
use App\Models\AepsWalletTransaction;
use App\Models\AepsCustomerService;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AepsController extends Controller
{
    // ── Dashboard page ──
    public function index()
    {
        return view('modules.aeps.index');
    }

    // ── Wallet balance ──
    public function walletBalance()
    {
        $wallet = AepsWallet::current();
        return response()->json(['balance' => $wallet->balance]);
    }

    // ── Wallet transactions list (with filters) ──
    public function walletTransactions(Request $request)
    {
        $data = AepsWalletTransaction::with('creator')
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->when($request->direction, fn($q, $d) => $q->where('direction', $d))
            ->when($request->date_from, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date_to, fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($request->search, fn($q, $s) => $q->where(function ($q2) use ($s) {
                $q2->where('reference', 'like', "%{$s}%")->orWhere('notes', 'like', "%{$s}%");
            }))
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json($data);
    }

    // ── Top-up wallet ──
    public function topUp(Request $request)
    {
        $data = $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string|max:50',
            'reference'      => 'nullable|string|max:150',
            'notes'          => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($data) {
            $wallet = AepsWallet::lockForUpdate()->first() ?? AepsWallet::create(['balance' => 0]);
            $wallet->credit($data['amount']);

            $txn = AepsWalletTransaction::create([
                'type'          => 'topup',
                'amount'        => $data['amount'],
                'direction'     => 'IN',
                'balance_after' => $wallet->balance,
                'payment_method'=> $data['payment_method'],
                'reference'     => $data['reference'] ?? null,
                'notes'         => $data['notes'] ?? null,
                'created_by'    => auth()->id(),
            ]);

            return response()->json(['success' => true, 'data' => $txn, 'balance' => $wallet->balance]);
        });
    }

    // ── Withdraw from wallet ──
    public function withdraw(Request $request)
    {
        $data = $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|max:50',
            'reference'      => 'nullable|string|max:150',
            'notes'          => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($data) {
            $wallet = AepsWallet::lockForUpdate()->first();
            if (!$wallet || $wallet->balance < $data['amount']) {
                return response()->json(['success' => false, 'message' => 'Insufficient wallet balance'], 422);
            }
            $wallet->debit($data['amount']);

            $txn = AepsWalletTransaction::create([
                'type'          => 'withdrawal',
                'amount'        => $data['amount'],
                'direction'     => 'OUT',
                'balance_after' => $wallet->balance,
                'payment_method'=> $data['payment_method'] ?? 'cash',
                'reference'     => $data['reference'] ?? null,
                'notes'         => $data['notes'] ?? null,
                'created_by'    => auth()->id(),
            ]);

            return response()->json(['success' => true, 'data' => $txn, 'balance' => $wallet->balance]);
        });
    }

    // ── Customer services list ──
    public function customerServices(Request $request)
    {
        $data = AepsCustomerService::with('customer', 'creator')
            ->when($request->service_type, fn($q, $t) => $q->where('service_type', $t))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->date_from, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date_to, fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($request->search, fn($q, $s) => $q->where(function ($q2) use ($s) {
                $q2->where('customer_name', 'like', "%{$s}%")
                   ->orWhere('bank_name', 'like', "%{$s}%")
                   ->orWhere('transaction_ref', 'like', "%{$s}%")
                   ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$s}%"));
            }))
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json($data);
    }

    // ── Record a customer service (cash withdrawal, balance enquiry, etc.) ──
    public function storeCustomerService(Request $request)
    {
        $data = $request->validate([
            'customer_id'       => 'nullable|exists:customers,id',
            'customer_name'     => 'required|string|max:150',
            'aadhaar_last4'     => 'nullable|digits:4',
            'service_type'      => 'required|in:cash_withdrawal,balance_enquiry,mini_statement,cash_deposit,aadhaar_pay',
            'amount'            => 'required|numeric|min:0',
            'bank_name'         => 'nullable|string|max:100',
            'transaction_ref'   => 'nullable|string|max:100',
            'status'            => 'nullable|in:success,failed,pending',
            'notes'             => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($data) {
            $data['created_by'] = auth()->id();
            $data['status'] = $data['status'] ?? 'success';

            $svc = AepsCustomerService::create($data);

            return response()->json([
                'success' => true,
                'data'    => $svc->load('customer', 'creator'),
            ]);
        });
    }

    // ── Dashboard stats ──
    public function stats(Request $request)
    {
        $from = $request->date_from ?: now()->startOfMonth()->toDateString();
        $to   = $request->date_to ?: now()->toDateString();

        $services = AepsCustomerService::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

        $totalCustomers    = (clone $services)->distinct('customer_name')->count('customer_name');
        $totalTransactions = (clone $services)->count();
        $totalAmount       = (clone $services)->where('status', 'success')->sum('amount');

        $byServiceType = AepsCustomerService::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('status', 'success')
            ->selectRaw("service_type, count(*) as count, sum(amount) as total_amount")
            ->groupBy('service_type')
            ->get();

        return response()->json([
            'balance'            => AepsWallet::current()->balance,
            'total_customers'    => $totalCustomers,
            'total_transactions' => $totalTransactions,
            'total_amount'       => $totalAmount,
            'by_service_type'    => $byServiceType,
        ]);
    }
}
