<?php

namespace Modules\Shared\Exceptions;

class InsufficientBalanceException extends DomainException
{
    public function __construct(string $message = 'Insufficient funds for this operation')
    {
        parent::__construct($message);
    }
}