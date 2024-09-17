<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';
}
