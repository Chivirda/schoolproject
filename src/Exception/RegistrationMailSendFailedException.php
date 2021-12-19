<?php

namespace App\Exception;

use Exception;

class RegistrationMailSendFailedException extends Exception
{
    private const MESSAGE = 'Registration mail send failed';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}