<?php

namespace App\Events;

use Modules\Purchasing\Models\PurchaseOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderReceived
{
    use Dispatchable, SerializesModels;

    public PurchaseOrder $purchaseOrder;

    /**
     * Create a new event instance.
     */
    public function __construct(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }
}
