<?php

namespace App\Services;

use App\Models\{Invoice, InvoiceItem, InvoicePayment, Product, Service, ServiceType, Inventory, PurchaseItem, StockMovement, LedgerTransaction, ActivityLog, TaxRate, Setting};
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $totalAmount = 0;
            $totalTax = 0;
            $totalCgst = 0;
            $totalSgst = 0;
            $totalIgst = 0;
            $items = $data['items'];

            // Determine if IGST applies (inter-state)
            $shopState = Setting::getValue('shop_state', '');
            $customerState = $data['customer_billing_state'] ?? '';
            $isIgst = $shopState && $customerState && $shopState !== $customerState;

            // Resolve tax rate for each item
            $defaultTaxRate = TaxRate::getDefault();

            foreach ($items as &$item) {
                $item['total'] = $item['quantity'] * $item['price'];
                $totalAmount += $item['total'];

                // Get tax rate + code: item-override > product/service master > default
                // Priority chain: tax_rate_override → product/service taxRate → HSN/SAC master → system default
                $taxRatePercent = 0;
                $hsnCode = $item['hsn_code'] ?? null;

                if (!empty($item['tax_rate_override'])) {
                    // Explicit override from the POS UI
                    $taxRatePercent = (float) $item['tax_rate_override'];
                } elseif ($item['item_type'] === 'product' && !empty($item['product_id'])) {
                    // Product item: use effectiveTaxPercent (tax_rate_id → HSN master → default)
                    $product = Product::with('taxRate')->find($item['product_id']);
                    if ($product) {
                        $hsnCode = $hsnCode ?: $product->hsn_code;
                        $taxRatePercent = $product->effective_tax_percent;
                    }
                } elseif ($item['item_type'] === 'service' && !empty($item['service_id'])) {
                    // Service item: resolve through Service → ServiceType → sac_code → TaxRate
                    $service = Service::with('serviceType.taxRate')->find($item['service_id']);
                    if ($service?->serviceType) {
                        $hsnCode = $hsnCode ?: $service->serviceType->sac_code; // store SAC in hsn_code column
                        $taxRatePercent = $service->serviceType->effective_tax_percent;
                    } elseif ($defaultTaxRate) {
                        $taxRatePercent = (float) $defaultTaxRate->percentage;
                    }
                } elseif ($defaultTaxRate) {
                    $taxRatePercent = (float) $defaultTaxRate->percentage;
                }

                // Calculate tax
                $taxableValue = $item['total'];
                $itemTax = round($taxableValue * $taxRatePercent / 100, 2);

                if ($isIgst) {
                    $item['igst_amount'] = $itemTax;
                    $item['cgst_amount'] = 0;
                    $item['sgst_amount'] = 0;
                    $totalIgst += $itemTax;
                } else {
                    $half = round($itemTax / 2, 2);
                    $item['cgst_amount'] = $half;
                    $item['sgst_amount'] = $itemTax - $half; // avoid rounding issues
                    $item['igst_amount'] = 0;
                    $totalCgst += $item['cgst_amount'];
                    $totalSgst += $item['sgst_amount'];
                }

                $item['tax_rate'] = $taxRatePercent;
                $item['tax_amount'] = $itemTax;
                $item['hsn_code'] = $hsnCode;
                $totalTax += $itemTax;
            }
            unset($item);

            $discount = $data['discount'] ?? 0;
            $finalAmount = $totalAmount + $totalTax - $discount;

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'customer_id' => $data['customer_id'] ?? null,
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'tax_amount' => $totalTax,
                'cgst_amount' => $totalCgst,
                'sgst_amount' => $totalSgst,
                'igst_amount' => $totalIgst,
                'is_igst' => $isIgst,
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
                    'hsn_code' => $item['hsn_code'] ?? null,
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'cgst_amount' => $item['cgst_amount'] ?? 0,
                    'sgst_amount' => $item['sgst_amount'] ?? 0,
                    'igst_amount' => $item['igst_amount'] ?? 0,
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
