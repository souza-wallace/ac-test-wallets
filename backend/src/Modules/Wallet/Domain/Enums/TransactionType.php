<?php

namespace Modules\Wallet\Domain\Enums;

enum TransactionType: string
{
    case DEPOSIT = 'DEPOSIT';
    case TRANSFER = 'TRANSFER';
    case REVERSAL = 'REVERSAL';
}