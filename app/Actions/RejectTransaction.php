<?php

namespace App\Actions;

use App\Contracts\TransactionAction;
use App\Enums\TransactionStatusEnum;
use App\Models\SystemPool;
use App\Models\Transaction;

class RejectTransaction extends TransactionAction
{
    public function handle(Transaction $transaction, SystemPool $systemPool): Transaction
    {
        return parent::execute($transaction, $systemPool, TransactionStatusEnum::REJECTED->value, false);
    }
}
