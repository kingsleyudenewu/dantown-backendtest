<?php

use App\Models\SystemPool;
use App\Models\Transaction;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\TransactionApproved;
use App\Notifications\TransactionRejected;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->wallet = Wallet::create(['user_id' => $this->user->id, 'balance' => 500]);
    $this->systemPool = SystemPool::create(['balance' => 1000000]);
});

it('should return a list of transactions', function () {
    $response = $this->get('/transactions');
    $response->assertStatus(200);
    // Assert that the response has the expected structure on a view and not JSON response
    $response->assertViewIs('transactions.index');
    // Assert that the response has the expected structure on a blade file
    $response->assertViewHas('transactions');

    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'amount',
                'status',
                'created_at',
                'updated_at',
            ],
        ],
    ]);
});


it('credits user wallet and debits system pool on approved credit transaction', function () {
    // Create a credit transaction
    $transaction = Transaction::create([
        'user_id' => $this->user->id,
        'type' => 'credit',
        'description' => 'Test credit transaction',
        'amount' => 100,
        'status' => 'pending',
    ]);

    // Approve the transaction
    $transaction->approve();

    // Assert that the user's wallet was credited
    expect($this->user->wallet->balance)->toBe(600);

    // Assert that the system pool was debited
    expect(SystemPool::first()->balance)->toBe(999900);
});

it('debits user wallet and credits system pool on approved debit transaction', function () {
    // Create a debit transaction
    $transaction = Transaction::create([
        'user_id' => $this->user->id,
        'type' => 'debit',
        'description' => 'Test debit transaction',
        'amount' => 50,
        'status' => 'pending',
    ]);

    // Approve the transaction
    $transaction->approve();

    // Assert that the user's wallet was debited
    expect($this->user->wallet->balance)->toBe(450);

    // Assert that the system pool was credited
    expect(SystemPool::first()->balance)->toBe(1000050);
});


it('does not modify wallet or system pool on rejected transaction', function () {
    // Create a pending transaction
    $transaction = Transaction::create([
        'user_id' => $this->user->id,
        'type' => 'credit',
        'description' => 'Test rejected transaction',
        'amount' => 100,
        'status' => 'pending',
    ]);

    // Reject the transaction
    $transaction->reject();

    // Assert that the user's wallet remains unchanged
    expect($this->user->wallet->balance)->toBe(500);

    // Assert that the system pool remains unchanged
    expect(SystemPool::first()->balance)->toBe(1000000);
});

it('should create a new transaction', function () {
    $response = $this->post('/transactions', [
        'amount' => 100,
        'status' => 'pending',
    ]);
    $response->assertStatus(201);
    $response->assertJsonStructure([
        'data' => [
            'id',
            'amount',
            'status',
            'created_at',
            'updated_at',
        ],
    ]);
});

//should approve a transaction on the transaction view page
it('should approve a transaction on the transaction view page', function () {
    $transaction = Transaction::factory()->create();
    $response = $this->get("/transactions/{$transaction->id}");
    $response->assertStatus(200);
    $response->assertViewIs('transactions.show');
    $response->assertViewHas('transaction');
});

//should reject a transaction on the transaction view page
it('should reject a transaction on the transaction view page', function () {
    $transaction = Transaction::factory()->create();
    $response = $this->get("/transactions/{$transaction->id}");
    $response->assertStatus(200);
    $response->assertViewIs('transactions.show');
    $response->assertViewHas('transaction');
});

it('should show the create transaction form', function () {
    $response = $this->get('/transactions/create');
    $response->assertStatus(200);
    $response->assertViewIs('transactions.create');
});

it('logs when a transaction is approved', function () {
    Log::shouldReceive('info')->once()
        ->with('Transaction 1 approved by user ID: 1');

    $transaction = Transaction::factory()->create(['status' => 'pending']);
    $transaction->approve();
});

it('sends notification when a transaction is approved', function () {
    Notification::fake();

    $transaction = Transaction::factory()->create(['status' => 'pending']);
    $transaction->approve();

    Notification::assertSentTo(
        $transaction->user,
        TransactionApproved::class
    );
});

it('sends notification when a transaction is rejected', function () {
    Notification::fake();

    $transaction = Transaction::factory()->create(['status' => 'pending']);
    $transaction->reject();

    Notification::assertSentTo(
        $transaction->user,
        TransactionRejected::class
    );
});

it('logs transaction history on approval', function () {
    $transaction = Transaction::factory()->create(['status' => 'pending', 'user_id' => $this->user->id]);
    $transaction->approve();

    $history = TransactionHistory::where('transaction_id', $transaction->id)->first();
    expect($history)->not->toBeNull();
    expect($history->action)->toBe('approved');
});
