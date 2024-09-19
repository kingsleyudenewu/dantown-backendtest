<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->reference = strtoupper(uniqid('TX-') . time());
        });

        // Log when a transaction is created
        static::created(function ($transaction) {
            Log::info("Transaction {$transaction->id} created by user ID: {$transaction->user_id}");
        });
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function histories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransactionHistory::class);
    }

    public static function createUserTx(
        User $user,
        float $amount,
        string $narration,
        string $type,
        bool $credit = false
    ): Transaction {
        $amount = ($credit ? 1 : -1) * $amount;

        return $user->transactions()->create([
            'amount' => $amount,
            'type' => $type,
            'ip_address' => request()->ip(),
            'narration' => $narration,
        ]);
    }
}
