<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function updateWalletBalance(float $amount, bool $credit = false)
    {
        $amount = ($credit ? 1 : -1) * $amount;

        return tap(auth()->user()->wallet)->update([
            'initial_amount' => auth()->user()->wallet->actual_amount,
            'actual_amount' => auth()->user()->wallet->actual_amount + $amount,
        ]);
    }
}
