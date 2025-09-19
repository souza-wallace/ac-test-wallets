<?php

namespace Modules\Shared\Exceptions;

class CannotReverseException extends DomainException
{
    public function __construct(string $message = 'Cannot reverse this transaction')
    {
        parent::__construct($message);
    }
}