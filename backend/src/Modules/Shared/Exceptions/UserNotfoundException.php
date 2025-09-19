<?php

namespace Modules\Shared\Exceptions;

class UserNotfoundException extends DomainException
{
    public function __construct(string $message = 'User not found')
    {
        parent::__construct($message);
    }
}