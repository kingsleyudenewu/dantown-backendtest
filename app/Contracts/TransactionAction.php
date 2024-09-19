<?php

namespace App\Contracts;

use App\Models\SystemPool;
use App\Models\Transaction;
use App\Models\TransactionHistory;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

abstract class TransactionAction
{
    /**
     * Execute the transaction action.
     *
     * @param Transaction $transaction
     * @param SystemPool $systemPool
     * @param string $status
     * @param bool $isCredit
     * @return Transaction
     */
    public function execute(Transaction $transaction, SystemPool $systemPool, string $status, bool $isCredit = true): Transaction
    {
        return DB::transaction(function () use ($transaction, $systemPool, $status, $isCredit) {
            // Update transaction status
            $this->updateTransactionStatus($transaction, $status);

            // Update wallet balance
            $this->updateWalletBalance($transaction, $isCredit);

            // Update system pool balance
            $this->updateSystemPoolBalance($systemPool, $transaction->amount, $isCredit);

            // Log transaction history
            $this->logTransactionHistory($transaction, $status);

            return $transaction;
        });
    }

    /**
     * Update the transaction status.
     *
     * @param Transaction $transaction
     * @param string $status
     * @return void
     */
    protected function updateTransactionStatus(Transaction $transaction, string $status): void
    {
        $transaction->update(['status' => $status]);
    }

    /**
     * Update the user's wallet balance.
     *
     * @param Transaction $transaction
     * @param bool $isCredit
     * @return void
     */
    protected function updateWalletBalance(Transaction $transaction, bool $isCredit): void
    {
        Wallet::updateWalletBalance($transaction->user, $transaction->amount, $isCredit);
    }

    /**
     * Update the system pool balance.
     *
     * @param SystemPool $systemPool
     * @param float $amount
     * @param bool $isCredit
     * @return void
     */
    protected function updateSystemPoolBalance(SystemPool $systemPool, float $amount, bool $isCredit): void
    {
        $newBalance = $isCredit ? $systemPool->balance - $amount : $systemPool->balance + $amount;
        $systemPool->update(['balance' => $newBalance]);
    }

    /**
     * Log the transaction history.
     *
     * @param Transaction $transaction
     * @param string $status
     * @return void
     */
    protected function logTransactionHistory(Transaction $transaction, string $status): void
    {
        TransactionHistory::create([
            'transaction_id' => $transaction->id,
            'user_id' => auth()->id(),
            'action' => $status,
        ]);
    }
}
