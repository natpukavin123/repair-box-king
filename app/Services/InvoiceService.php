<?php

namespace App\Services;

use App\Models\{Invoice, InvoiceItem, InvoicePayment, Product, Service, ServiceType, Inventory, PurchaseItem, StockMovement, LedgerTransaction, ActivityLog};
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $totalAmount = 0;
            $items = $data['items'];

            foreach ($items as &$item) {
                $item['total'] = $item['quantity'] * $item['price'];
                $totalAmount += $item['total'];
            }
            unset($item);

            $discount = $data['discount'] ?? 0;
            $finalAmount = $totalAmount - $discount;

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'customer_id' => $data['customer_id'] ?? null,
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'final_amount' => $finalAmount,
                'payment_status' => 'unpaid',
                'created_by' => auth()->id(),
            ]);

            foreach ($items as $item) {
                $invoiceItem = InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => $item['item_type'],
                    'product_id' => $item['product_id'] ?? null,
                    'service_id' => $item['service_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'mrp' => $item['mrp'] ?? $item['price'],
                    'total' => $item['total'],
                    'is_linked' => $item['is_linked'] ?? false,
                    'linked_id' => $item['linked_id'] ?? null,
                ]);

                // Deduct stock for product items using FIFO
                if ($item['item_type'] === 'product' && !empty($item['product_id'])) {
                    $this->deductStockFIFO($item['product_id'], $item['quantity'], $invoiceItem->id);
                }
            }

            // Process payments (optional – omit to create draft/unpaid invoice)
            $payments = $data['payments'] ?? [];
            $totalPaid = 0;
            foreach ($payments as $payment) {
                InvoicePayment::create([
                    'invoice_id' => $invoice->id,
                    'payment_method' => $payment['payment_method'],
                    'amount' => $payment['amount'],
                    'transaction_reference' => $payment['transaction_reference'] ?? null,
                ]);
                $totalPaid += $payment['amount'];
            }

            // Update payment status
            if ($totalPaid >= $finalAmount) {
                $invoice->update(['payment_status' => 'paid', 'is_locked' => true]);
            } elseif ($totalPaid > 0) {
                $invoice->update(['payment_status' => 'partial']);
            }

            // Only record ledger & customer stats when payment is received
            if ($totalPaid > 0) {
                // Update customer stats
                if ($invoice->customer_id) {
                    $customer = $invoice->customer;
                    $customer->increment('total_spent', $totalPaid);
                    $customer->increment('loyalty_points', intval($totalPaid / 100));
                    $customer->update(['last_visit' => now()]);
                }

                LedgerTransaction::create([
                    'transaction_type' => 'sale',
                    'reference_module' => 'invoices',
                    'reference_id' => $invoice->id,
                    'amount' => $totalPaid,
                    'payment_method' => count($payments) > 1 ? 'split' : $payments[0]['payment_method'],
                    'direction' => 'IN',
                    'description' => "Invoice {$invoice->invoice_number}",
                    'created_by' => auth()->id(),
                ]);
            }

            ActivityLog::log('create', 'invoices', $invoice->id, "Created invoice {$invoice->invoice_number}");

            return $invoice->load('items', 'payments', 'customer');
        });
    }

    public function addPayment(Invoice $invoice, array $payments): Invoice
    {
        return DB::transaction(function () use ($invoice, $payments) {
            $totalNewPayment = 0;
            foreach ($payments as $payment) {
                InvoicePayment::create([
                    'invoice_id' => $invoice->id,
                    'payment_method' => $payment['payment_method'],
                    'amount' => $payment['amount'],
                    'transaction_reference' => $payment['transaction_reference'] ?? null,
                ]);
                $totalNewPayment += $payment['amount'];
            }

            $totalPaid = $invoice->payments()->sum('amount');

            if ($totalPaid >= $invoice->final_amount) {
                $invoice->update(['payment_status' => 'paid', 'is_locked' => true]);
            } elseif ($totalPaid > 0) {
                $invoice->update(['payment_status' => 'partial']);
            }

            // Ledger entry for this batch of payments
            LedgerTransaction::create([
                'transaction_type' => 'sale',
                'reference_module' => 'invoices',
                'reference_id' => $invoice->id,
                'amount' => $totalNewPayment,
                'payment_method' => count($payments) > 1 ? 'split' : $payments[0]['payment_method'],
                'direction' => 'IN',
                'description' => "Payment for Invoice {$invoice->invoice_number}",
                'created_by' => auth()->id(),
            ]);

            // Update customer stats if fully paid for first time
            if ($invoice->wasChanged('payment_status') && $invoice->payment_status === 'paid' && $invoice->customer_id) {
                $customer = $invoice->customer;
                $customer->increment('total_spent', $invoice->final_amount);
                $customer->increment('loyalty_points', intval($invoice->final_amount / 100));
                $customer->update(['last_visit' => now()]);
            }

            ActivityLog::log('payment', 'invoices', $invoice->id, "Payment of ₹{$totalNewPayment} recorded for invoice {$invoice->invoice_number}");

            return $invoice->fresh()->load('items', 'payments', 'customer');
        });
    }

    private function deductStockFIFO(int $productId, int $quantity, int $invoiceItemId): void
    {
        $remaining = $quantity;
        $purchaseItems = PurchaseItem::where('product_id', $productId)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('id')
            ->get();

        foreach ($purchaseItems as $pi) {
            if ($remaining <= 0) break;
            $deduct = min($remaining, $pi->remaining_quantity);
            $pi->decrement('remaining_quantity', $deduct);
            StockMovement::create([
                'product_id' => $productId,
                'purchase_item_id' => $pi->id,
                'invoice_item_id' => $invoiceItemId,
                'quantity' => $deduct,
                'cost_price' => $pi->purchase_price,
            ]);
            $remaining -= $deduct;
        }

        // Update inventory
        $inventory = Inventory::where('product_id', $productId)->first();
        if ($inventory) {
            $inventory->decrement('current_stock', $quantity);
        }
    }
}
