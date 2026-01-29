<?php

namespace App\Events;

use Modules\Sales\Models\SalesOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalesOrderCreated
{
    use Dispatchable, SerializesModels;

    public SalesOrder $salesOrder;

    /**
     * Create a new event instance.
     */
    public function __construct(SalesOrder $salesOrder)
    {
        $this->salesOrder = $salesOrder;
    }
}
