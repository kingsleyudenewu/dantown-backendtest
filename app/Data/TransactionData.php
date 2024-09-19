<?php

namespace App\Data;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class TransactionData extends Data
{
    public function __construct(
        public string $type,
        public string $narration,
        public float $amount,
    ) {
    }

    public static function stopOnFirstFailure(): bool
    {
        return true;
    }

    /**
     * @return array<string, array>
     */
    public static function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::enum(TransactionTypeEnum::class)],
            'narration' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
        ];
    }
}
