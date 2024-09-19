<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemPool extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function execute(float $amount, bool $credit = false)
    {
        $amount = ($credit ? 1 : -1) * $amount;

        return $this->balance + $amount;
    }
}
