<?php

namespace App\Listeners;

use App\Events\SalesOrderDelivered;
use Modules\Finance\Models\Journal;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecordSalesRevenue
{
    /**
     * Handle the event - Create journal entry for sales revenue
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
            // Get accounts (you may need to adjust account codes based on your chart of accounts)
            $accountsReceivable = Account::where('code', '1200')->first(); // Accounts Receivable
            $salesRevenueAccount = Account::where('code', '4000')->first(); // Sales Revenue
            $taxPayableAccount = Account::where('code', '2100')->first(); // Tax Payable

            if (!$accountsReceivable || !$salesRevenueAccount) {
                Log::warning('Required accounts not found for sales journal entry');
                return;
            }

            // Create journal
            $journal = Journal::create([
                'date' => now(),
                'reference' => "SO-{$salesOrder->order_number}",
                'description' => "Sales Order #{$salesOrder->order_number} - Revenue Recognition",
                'status' => 'posted',
                'user_id' => auth()->id(),
            ]);

            // Debit: Accounts Receivable (Total Amount)
            JournalEntry::create([
                'journal_id' => $journal->id,
                'account_id' => $accountsReceivable->id,
                'debit' => $salesOrder->total,
                'credit' => 0,
                'description' => "A/R for Sales Order #{$salesOrder->order_number}",
            ]);

            // Credit: Sales Revenue (Subtotal)
            JournalEntry::create([
                'journal_id' => $journal->id,
                'account_id' => $salesRevenueAccount->id,
                'debit' => 0,
                'credit' => $salesOrder->subtotal,
                'description' => "Revenue from Sales Order #{$salesOrder->order_number}",
            ]);

            // Credit: Tax Payable (if applicable)
            if ($salesOrder->tax_amount > 0 && $taxPayableAccount) {
                JournalEntry::create([
                    'journal_id' => $journal->id,
                    'account_id' => $taxPayableAccount->id,
                    'debit' => 0,
                    'credit' => $salesOrder->tax_amount,
                    'description' => "Tax on Sales Order #{$salesOrder->order_number}",
                ]);
            }

            Log::info("Sales revenue recorded for order {$salesOrder->order_number}");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording sales revenue: ' . $e->getMessage());
            throw $e;
        }
    }
}
