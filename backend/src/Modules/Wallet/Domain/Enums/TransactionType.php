<?php

namespace Modules\Wallet\Domain\Enums;

enum TransactionType: string
{
    case DEPOSIT = 'deposit';
    case TRANSFER = 'transfer';
    case REVERSAL = 'reversal';
}