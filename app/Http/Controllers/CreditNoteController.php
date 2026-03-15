<?php

namespace App\Http\Controllers;

use App\Models\{CreditNote, CreditNoteItem, CreditNoteRefund, Invoice, Repair, LedgerTransaction, ActivityLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditNoteController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = CreditNote::with('customer', 'creator')
                ->when(request('search'), fn($q, $s) => $q->where('credit_note_number', 'like', "%{$s}%")
                    ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$s}%")))
                ->when(request('status'), fn($q, $s) => $q->where('status', $s))
                ->when(request('source_type'), fn($q, $t) => $q->where('source_type', $t))
                ->when(request('date_from'), fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when(request('date_to'), fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->latest()
                ->paginate(request('per_page', 15));
            return response()->json($data);
        }
        return view('modules.credit-notes.index');
    }

    /**
     * Show form to create CN from an invoice.
     */
    public function createFromInvoice(Invoice $invoice)
    {
        $invoice->load('items', 'customer', 'creditNotes.items');

        // Calculate already credited quantities per invoice item
        $creditedQuantities = [];
        foreach ($invoice->creditNotes->whereNotIn('status', ['cancelled']) as $cn) {
            foreach ($cn->items as $item) {
                if ($item->original_item_id) {
                    $creditedQuantities[$item->original_item_id] = ($creditedQuantities[$item->original_item_id] ?? 0) + $item->quantity;
                }
            }
        }

        return view('modules.credit-notes.create', [
            'source' => $invoice,
            'sourceType' => 'invoice',
            'creditedQuantities' => $creditedQuantities,
        ]);
    }

    /**
     * Show form to create CN from a repair.
     */
    public function createFromRepair(Repair $repair)
    {
        $repair->load('customer', 'parts.part', 'repairServices.serviceType');

        // Calculate already credited quantities
        $existingCNs = CreditNote::where('source_type', 'repair')
            ->where('source_id', $repair->id)
            ->whereNotIn('status', ['cancelled'])
            ->with('items')
            ->get();

        $creditedQuantities = [];
        foreach ($existingCNs as $cn) {
            foreach ($cn->items as $item) {
                if ($item->original_item_id) {
                    $key = $item->item_type . '_' . $item->original_item_id;
                    $creditedQuantities[$key] = ($creditedQuantities[$key] ?? 0) + $item->quantity;
                }
            }
        }

        return view('modules.credit-notes.create', [
            'source' => $repair,
            'sourceType' => 'repair',
            'creditedQuantities' => $creditedQuantities,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'source_type' => 'required|in:invoice,repair',
            'source_id' => 'required|integer',
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|in:product,part,service',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'items.*.original_item_id' => 'nullable|integer',
        ]);

        // Verify source exists
        if ($data['source_type'] === 'invoice') {
            $source = Invoice::findOrFail($data['source_id']);
            $customerId = $source->customer_id;
        } else {
            $source = Repair::findOrFail($data['source_id']);
            $customerId = $source->customer_id;
        }

        return DB::transaction(function () use ($data, $customerId) {
            $totalAmount = array_sum(array_column($data['items'], 'total'));

            $cn = CreditNote::create([
                'credit_note_number' => CreditNote::generateCreditNoteNumber(),
                'source_type' => $data['source_type'],
                'source_id' => $data['source_id'],
                'customer_id' => $customerId,
                'total_amount' => $totalAmount,
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                CreditNoteItem::create([
                    'credit_note_id' => $cn->id,
                    'item_type' => $item['item_type'],
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                    'original_item_id' => $item['original_item_id'] ?? null,
                ]);
            }

            ActivityLog::log('create', 'credit_notes', $cn->id,
                "Created credit note {$cn->credit_note_number} — ₹" . number_format($totalAmount, 2));

            return response()->json([
                'success' => true,
                'data' => $cn->load('items', 'customer'),
                'message' => "Credit Note {$cn->credit_note_number} created successfully",
                'redirect' => "/credit-notes/{$cn->id}",
            ]);
        });
    }

    public function show(CreditNote $creditNote)
    {
        if (request()->ajax()) {
            return response()->json(
                $creditNote->load('items', 'refunds.processor', 'customer', 'creator', 'approver')
            );
        }

        $creditNote->load('items', 'refunds.processor', 'customer', 'creator', 'approver');

        // Load source
        if ($creditNote->source_type === 'invoice') {
            $creditNote->load('sourceInvoice');
        } else {
            $creditNote->load('sourceRepair');
        }

        return view('modules.credit-notes.show', compact('creditNote'));
    }

    public function approve(CreditNote $creditNote)
    {
        if ($creditNote->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Only draft credit notes can be approved.'], 422);
        }

        $creditNote->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        ActivityLog::log('update', 'credit_notes', $creditNote->id,
            "Approved credit note {$creditNote->credit_note_number}");

        return response()->json([
            'success' => true,
            'message' => "Credit Note {$creditNote->credit_note_number} approved",
        ]);
    }

    public function processRefund(Request $request, CreditNote $creditNote)
    {
        if (!in_array($creditNote->status, ['approved', 'partially_refunded'])) {
            return response()->json([
                'success' => false,
                'message' => 'Refunds can only be processed against approved credit notes.',
            ], 422);
        }

        $remaining = $creditNote->remainingRefundable();

        $data = $request->validate([
            'amount' => "required|numeric|min:0.01|max:{$remaining}",
            'method' => 'required|string|in:cash,upi,bank_transfer,card',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($creditNote, $data) {
            $refund = CreditNoteRefund::create([
                'credit_note_id' => $creditNote->id,
                'resolution_type' => 'refund',
                'amount' => $data['amount'],
                'method' => $data['method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            $newRefunded = $creditNote->refunded_amount + $data['amount'];
            $creditNote->update([
                'refunded_amount' => $newRefunded,
                'status' => $newRefunded >= $creditNote->total_amount ? 'fully_refunded' : 'partially_refunded',
            ]);

            // Record in ledger as OUT
            LedgerTransaction::create([
                'transaction_type' => $creditNote->source_type === 'invoice' ? 'sale' : 'repair',
                'reference_module' => 'credit_notes',
                'reference_id' => $creditNote->id,
                'amount' => $data['amount'],
                'payment_method' => $data['method'],
                'direction' => 'OUT',
                'description' => "Refund via {$creditNote->credit_note_number} — ₹" . number_format($data['amount'], 2),
                'created_by' => auth()->id(),
            ]);

            // If source is repair, also record as OUT payment on the repair
            if ($creditNote->source_type === 'repair') {
                $repair = Repair::find($creditNote->source_id);
                if ($repair) {
                    $repair->payments()->create([
                        'payment_type' => 'refund',
                        'amount' => $data['amount'],
                        'payment_method' => $data['method'],
                        'reference_number' => $data['reference_number'] ?? null,
                        'direction' => 'OUT',
                        'notes' => "Credit Note refund ({$creditNote->credit_note_number})",
                    ]);
                }
            }

            ActivityLog::log('update', 'credit_notes', $creditNote->id,
                "Refund ₹" . number_format($data['amount'], 2) . " via {$data['method']} against {$creditNote->credit_note_number}");

            return response()->json([
                'success' => true,
                'message' => '₹' . number_format($data['amount'], 2) . ' refunded successfully',
            ]);
        });
    }

    /**
     * Apply credit note toward a new repair order.
     */
    public function applyToRepair(Request $request, CreditNote $creditNote)
    {
        if (!in_array($creditNote->status, ['approved', 'partially_refunded'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only approved credit notes can be applied to a new repair.',
            ], 422);
        }

        $remaining = $creditNote->remainingRefundable();

        $data = $request->validate([
            'repair_id' => 'required|exists:repairs,id',
            'amount' => "required|numeric|min:0.01|max:{$remaining}",
        ]);

        return DB::transaction(function () use ($creditNote, $data) {
            $repair = Repair::findOrFail($data['repair_id']);

            // Record CN usage as resolution
            CreditNoteRefund::create([
                'credit_note_id' => $creditNote->id,
                'resolution_type' => 'new_repair',
                'reference_type' => 'repairs',
                'reference_id' => $repair->id,
                'amount' => $data['amount'],
                'method' => 'credit_note',
                'notes' => "Applied to repair {$repair->ticket_number}",
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Add as advance payment on the repair
            $repair->payments()->create([
                'payment_type' => 'advance',
                'amount' => $data['amount'],
                'payment_method' => 'credit_note',
                'reference_number' => $creditNote->credit_note_number,
                'direction' => 'IN',
                'notes' => "Credit from {$creditNote->credit_note_number}",
            ]);

            // Update CN balance
            $newRefunded = $creditNote->refunded_amount + $data['amount'];
            $creditNote->update([
                'refunded_amount' => $newRefunded,
                'status' => $newRefunded >= $creditNote->total_amount ? 'fully_refunded' : 'partially_refunded',
            ]);

            // Ledger
            LedgerTransaction::create([
                'transaction_type' => 'repair',
                'reference_module' => 'credit_notes',
                'reference_id' => $creditNote->id,
                'amount' => $data['amount'],
                'payment_method' => 'credit_note',
                'direction' => 'IN',
                'description' => "Credit {$creditNote->credit_note_number} applied to repair {$repair->ticket_number}",
                'created_by' => auth()->id(),
            ]);

            ActivityLog::log('update', 'credit_notes', $creditNote->id,
                "Applied ₹" . number_format($data['amount'], 2) . " to repair {$repair->ticket_number}");

            return response()->json([
                'success' => true,
                'message' => '₹' . number_format($data['amount'], 2) . " applied to repair {$repair->ticket_number}",
                'redirect' => "/repairs/{$repair->id}",
            ]);
        });
    }

    /**
     * Apply credit note toward a new invoice (POS sale).
     */
    public function applyToInvoice(Request $request, CreditNote $creditNote)
    {
        if (!in_array($creditNote->status, ['approved', 'partially_refunded'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only approved credit notes can be applied to a new invoice.',
            ], 422);
        }

        $remaining = $creditNote->remainingRefundable();

        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => "required|numeric|min:0.01|max:{$remaining}",
        ]);

        return DB::transaction(function () use ($creditNote, $data) {
            $invoice = Invoice::findOrFail($data['invoice_id']);

            // Record CN usage
            CreditNoteRefund::create([
                'credit_note_id' => $creditNote->id,
                'resolution_type' => 'new_invoice',
                'reference_type' => 'invoices',
                'reference_id' => $invoice->id,
                'amount' => $data['amount'],
                'method' => 'credit_note',
                'notes' => "Applied to invoice {$invoice->invoice_number}",
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Add as payment on the invoice
            $invoice->payments()->create([
                'payment_method' => 'credit_note',
                'amount' => $data['amount'],
                'transaction_reference' => $creditNote->credit_note_number,
            ]);

            // Recalculate invoice payment status
            $totalPaid = $invoice->payments()->sum('amount');
            if ($totalPaid >= $invoice->final_amount) {
                $invoice->update(['payment_status' => 'paid', 'is_locked' => true]);
            } elseif ($totalPaid > 0) {
                $invoice->update(['payment_status' => 'partial']);
            }

            // Update CN balance
            $newRefunded = $creditNote->refunded_amount + $data['amount'];
            $creditNote->update([
                'refunded_amount' => $newRefunded,
                'status' => $newRefunded >= $creditNote->total_amount ? 'fully_refunded' : 'partially_refunded',
            ]);

            ActivityLog::log('update', 'credit_notes', $creditNote->id,
                "Applied ₹" . number_format($data['amount'], 2) . " to invoice {$invoice->invoice_number}");

            return response()->json([
                'success' => true,
                'message' => '₹' . number_format($data['amount'], 2) . " applied to invoice {$invoice->invoice_number}",
            ]);
        });
    }

    public function cancel(CreditNote $creditNote)
    {
        if (!in_array($creditNote->status, ['draft', 'approved'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only draft or approved credit notes can be cancelled.',
            ], 422);
        }

        $creditNote->update(['status' => 'cancelled']);

        ActivityLog::log('update', 'credit_notes', $creditNote->id,
            "Cancelled credit note {$creditNote->credit_note_number}");

        return response()->json([
            'success' => true,
            'message' => "Credit Note {$creditNote->credit_note_number} cancelled",
        ]);
    }
}
