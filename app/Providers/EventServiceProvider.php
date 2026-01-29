<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Sales Module Events
        \App\Events\SalesOrderCreated::class => [
            // Add listeners here if needed
        ],
        \App\Events\SalesOrderDelivered::class => [
            \App\Listeners\DeductInventoryFromSales::class,
            \App\Listeners\RecordSalesRevenue::class,
        ],
        
        // Purchasing Module Events
        \App\Events\PurchaseOrderReceived::class => [
            \App\Listeners\AddInventoryFromPurchase::class,
            \App\Listeners\RecordPurchaseExpense::class,
        ],
        
        // Finance Module Events
        \App\Events\InvoicePaid::class => [
            \App\Listeners\RecordPaymentReceipt::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
