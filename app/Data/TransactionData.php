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
        public int $user_id,
        public string $narration,
        public float $amount,
        public string $status,
    ) {}

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
            'user_id' => ['required', 'integer', 'exists:projects,id'],
            'narration' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'status' => ['required', 'string', Rule::enum(TransactionStatusEnum::class)],
        ];
    }
}
