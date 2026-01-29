<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use Modules\Finance\Models\Journal;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecordPaymentReceipt
{
    /**
     * Handle the event - Create journal entry for payment received
     */
    public function handle(InvoicePaid $event): void
    {
        $invoice = $event->invoice;
        $amount = $event->amount;

        DB::beginTransaction();
        try {
            // Get accounts
            $cashAccount = Account::where('code', '1000')->first(); // Cash/Bank
            $accountsReceivable = Account::where('code', '1200')->first(); // Accounts Receivable

            if (!$cashAccount || !$accountsReceivable) {
                Log::warning('Required accounts not found for payment journal entry');
                return;
            }

            // Create journal
            $journal = Journal::create([
                'date' => now(),
                'reference' => "PAY-{$invoice->invoice_number}",
                'description' => "Payment received for Invoice #{$invoice->invoice_number}",
                'status' => 'posted',
                'user_id' => auth()->id(),
            ]);

            // Debit: Cash/Bank
            JournalEntry::create([
                'journal_id' => $journal->id,
                'account_id' => $cashAccount->id,
                'debit' => $amount,
                'credit' => 0,
                'description' => "Payment received for Invoice #{$invoice->invoice_number}",
            ]);

            // Credit: Accounts Receivable
            JournalEntry::create([
                'journal_id' => $journal->id,
                'account_id' => $accountsReceivable->id,
                'debit' => 0,
                'credit' => $amount,
                'description' => "Payment received for Invoice #{$invoice->invoice_number}",
            ]);

            Log::info("Payment receipt recorded for invoice {$invoice->invoice_number}, amount: {$amount}");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording payment receipt: ' . $e->getMessage());
            throw $e;
        }
    }
}
