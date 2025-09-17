<?php

namespace Modules\Shared\Exceptions;

class InsufficientFundsException extends DomainException
{
    public function __construct(string $message = 'Insufficient funds for this operation')
    {
        parent::__construct($message);
    }
}