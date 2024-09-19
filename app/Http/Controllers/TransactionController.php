<?php

namespace App\Http\Controllers;

use App\Actions\ApproveTransaction;
use App\Actions\RejectTransaction;
use App\Data\TransactionData;
use App\Models\SystemPool;
use App\Models\Transaction;
use App\Notifications\TransactionApproved;
use App\Notifications\TransactionRejected;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('user')->latest()->paginate();

        return view('transactions.index', compact('transactions'));
    }

    public function store(Request $request)
    {
        $txData = TransactionData::from($request);

        Transaction::create($txData->toArray());

        return redirect()->route('transactions.index')->with('messages', 'Transaction created successfully.');
    }

    public function approve(Transaction $transaction)
    {
        $systemPool = SystemPool::first();

        if ($systemPool->balance < $transaction->amount) {
            return redirect()->route('transactions.index')->with('messages', 'Insufficient funds in system pool.');
        }

        $approvedTransaction = (new ApproveTransaction())->handle($transaction, $systemPool);

        $transaction->user->notify(new TransactionApproved($approvedTransaction));

        return redirect()->route('transactions.index')->with('messages', 'Transaction approved successfully.');
    }

    public function reject(Transaction $transaction)
    {
        $systemPool = SystemPool::first();

        $rejectedTransaction = (new RejectTransaction())->handle($transaction, $systemPool);

        $transaction->user->notify(new TransactionRejected($rejectedTransaction));

        return redirect()->route('transactions.index')->with('messages', 'Transaction rejected.');
    }
}
