<?php

namespace App\Services;

use App\Models\{Invoice, InvoiceItem, InvoicePayment, Product, Inventory, PurchaseItem, StockMovement, LedgerTransaction, ActivityLog};
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
            unset($item); // break reference to last element to avoid lingering reference issues

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
                    'total' => $item['total'],
                ]);

                // Deduct stock for product items using FIFO
                if ($item['item_type'] === 'product' && !empty($item['product_id'])) {
                    $this->deductStockFIFO($item['product_id'], $item['quantity'], $invoiceItem->id);
                }
            }

            // Process payments
            $totalPaid = 0;
            foreach ($data['payments'] as $payment) {
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

            // Update customer stats
            if ($invoice->customer_id) {
                $customer = $invoice->customer;
                $customer->increment('total_spent', $finalAmount);
                $customer->increment('loyalty_points', intval($finalAmount / 100));
                $customer->update(['last_visit' => now()]);
            }

            // Ledger entry
            LedgerTransaction::create([
                'transaction_type' => 'sale',
                'reference_module' => 'invoices',
                'reference_id' => $invoice->id,
                'amount' => $finalAmount,
                'payment_method' => count($data['payments']) > 1 ? 'split' : $data['payments'][0]['payment_method'],
                'direction' => 'IN',
                'description' => "Invoice {$invoice->invoice_number}",
                'created_by' => auth()->id(),
            ]);

            ActivityLog::log('create', 'invoices', $invoice->id, "Created invoice {$invoice->invoice_number}");

            return $invoice->load('items', 'payments', 'customer');
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
