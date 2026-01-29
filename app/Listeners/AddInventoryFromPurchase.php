<?php

namespace App\Listeners;

use App\Events\PurchaseOrderReceived;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\StockLevel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddInventoryFromPurchase
{
    /**
     * Handle the event - Add stock when purchase order is received
     */
    public function handle(PurchaseOrderReceived $event): void
    {
        $purchaseOrder = $event->purchaseOrder;

        DB::beginTransaction();
        try {
            foreach ($purchaseOrder->items as $item) {
                // Skip if no quantity received
                if ($item->received_quantity <= 0) {
                    continue;
                }

                // Get warehouse from goods receipt or use default
                $warehouse = \Modules\Inventory\Models\Warehouse::where('is_default', true)->first();
                
                if (!$warehouse) {
                    Log::warning('No default warehouse found for purchase order receipt');
                    continue;
                }

                // Find or create stock level
                $stockLevel = StockLevel::firstOrCreate(
                    [
                        'product_id' => $item->product_id,
                        'warehouse_id' => $warehouse->id,
                    ],
                    [
                        'quantity' => 0,
                        'reserved_quantity' => 0,
                    ]
                );

                // Add stock
                $stockLevel->increment('quantity', $item->received_quantity);

                // Record stock movement
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $warehouse->id,
                    'type' => 'in',
                    'quantity' => $item->received_quantity,
                    'reference_type' => 'purchase_order',
                    'reference_id' => $purchaseOrder->id,
                    'user_id' => auth()->id(),
                    'notes' => "Stock received from Purchase Order #{$purchaseOrder->po_number}",
                ]);

                Log::info("Stock added for product {$item->product_id}, quantity: {$item->received_quantity}");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding inventory from purchase: ' . $e->getMessage());
            throw $e;
        }
    }
}
