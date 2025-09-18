<?php

namespace Modules\Wallet\Domain\Enums;

enum TransactionStatus: string
{
    case PENDING = 'PENDING';
    case COMPLETED = 'COMPLETED';
    case REVERSED = 'REVERSED';
}
