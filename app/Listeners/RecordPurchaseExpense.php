<?php

namespace App\Listeners;

use App\Events\PurchaseOrderReceived;
use Modules\Finance\Models\Journal;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecordPurchaseExpense
{
    /**
     * Handle the event - Create journal entry for purchase expense
     */
    public function handle(PurchaseOrderReceived $event): void
    {
        $purchaseOrder = $event->purchaseOrder;

        DB::beginTransaction();
        try {
            // Get accounts
            $inventoryAccount = Account::where('code', '1300')->first(); // Inventory
            $accountsPayable = Account::where('code', '2000')->first(); // Accounts Payable
            $taxReceivableAccount = Account::where('code', '1500')->first(); // Tax Receivable

            if (!$inventoryAccount || !$accountsPayable) {
                Log::warning('Required accounts not found for purchase journal entry');
                return;
            }

            // Create journal
            $journal = Journal::create([
                'date' => now(),
                'reference' => "PO-{$purchaseOrder->po_number}",
                'description' => "Purchase Order #{$purchaseOrder->po_number} - Inventory Purchase",
                'status' => 'posted',
                'user_id' => auth()->id(),
            ]);

            // Debit: Inventory (Subtotal)
            JournalEntry::create([
                'journal_id' => $journal->id,
                'account_id' => $inventoryAccount->id,
                'debit' => $purchaseOrder->subtotal,
                'credit' => 0,
                'description' => "Inventory from PO #{$purchaseOrder->po_number}",
            ]);

            // Debit: Tax Receivable (if applicable)
            if ($purchaseOrder->tax_amount > 0 && $taxReceivableAccount) {
                JournalEntry::create([
                    'journal_id' => $journal->id,
                    'account_id' => $taxReceivableAccount->id,
                    'debit' => $purchaseOrder->tax_amount,
                    'credit' => 0,
                    'description' => "Tax on PO #{$purchaseOrder->po_number}",
                ]);
            }

            // Credit: Accounts Payable (Total Amount)
            JournalEntry::create([
                'journal_id' => $journal->id,
                'account_id' => $accountsPayable->id,
                'debit' => 0,
                'credit' => $purchaseOrder->total_amount,
                'description' => "A/P for PO #{$purchaseOrder->po_number}",
            ]);

            Log::info("Purchase expense recorded for PO {$purchaseOrder->po_number}");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording purchase expense: ' . $e->getMessage());
            throw $e;
        }
    }
}
