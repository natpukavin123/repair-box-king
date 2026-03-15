<?php

namespace App\Services;

use App\Models\{Purchase, PurchaseItem, Inventory, LedgerTransaction, ActivityLog};
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['purchase_price'];
            }

            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'purchase_date' => $data['purchase_date'],
                'invoice_number' => $data['invoice_number'] ?? null,
                'total_amount' => $totalAmount,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'remaining_quantity' => $item['quantity'],
                ]);

                // Update inventory
                $inventory = Inventory::firstOrCreate(
                    ['product_id' => $item['product_id']],
                    ['current_stock' => 0, 'reserved_stock' => 0]
                );
                $inventory->increment('current_stock', $item['quantity']);
            }

            LedgerTransaction::create([
                'transaction_type' => 'purchase',
                'reference_module' => 'purchases',
                'reference_id' => $purchase->id,
                'amount' => $totalAmount,
                'payment_method' => 'bank',
                'direction' => 'OUT',
                'description' => "Purchase from {$purchase->supplier->name}",
                'created_by' => auth()->id(),
            ]);

            ActivityLog::log('create', 'purchases', $purchase->id, "Purchase #{$purchase->invoice_number}");

            return $purchase->load('items.product', 'supplier');
        });
    }
}
