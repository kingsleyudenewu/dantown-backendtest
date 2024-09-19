<?php

namespace Database\Factories;

use App\Enums\TransactionStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'narration' => $this->faker->sentence,
            'amount' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }

    public function pending()
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatusEnum::PENDING->value,
        ]);
    }

    public function approved()
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatusEnum::APPROVED->value,
        ]);
    }

    public function rejected()
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatusEnum::REJECTED->value,
        ]);
    }
}
