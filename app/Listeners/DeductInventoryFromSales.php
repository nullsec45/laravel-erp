<?php

namespace App\Listeners;

use App\Events\SalesOrderDelivered;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\StockLevel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeductInventoryFromSales
{
    /**
     * Handle the event - Deduct stock when sales order is delivered
     */
    public function handle(SalesOrderDelivered $event): void
    {
        $salesOrder = $event->salesOrder;

        // Only process if status is 'delivered'
        if ($salesOrder->status !== 'delivered') {
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($salesOrder->items as $item) {
                // Get default warehouse (you can make this configurable)
                $warehouse = \Modules\Inventory\Models\Warehouse::where('is_default', true)->first();
                
                if (!$warehouse) {
                    Log::warning('No default warehouse found for sales order deduction');
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

                // Deduct stock
                $stockLevel->decrement('quantity', $item->quantity);

                // Record stock movement
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $warehouse->id,
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'reference_type' => 'sales_order',
                    'reference_id' => $salesOrder->id,
                    'user_id' => auth()->id(),
                    'notes' => "Stock deducted for Sales Order #{$salesOrder->order_number}",
                ]);

                Log::info("Stock deducted for product {$item->product_id}, quantity: {$item->quantity}");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deducting inventory from sales: ' . $e->getMessage());
            throw $e;
        }
    }
}
