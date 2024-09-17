<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
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
            'reference' => strtoupper(uniqid('TX-').time()),
            'ip_address' => request()->ip(),
            'narration' => $narration,
        ]);
    }
}
