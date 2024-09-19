<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Maker can create transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create')->middleware('role:maker');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store')->middleware('role:maker');

    // Checker can approve or reject transactions
    Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve')->middleware('role:checker');
    Route::post('/transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject')->middleware('role:checker');
});

require __DIR__ . '/auth.php';
