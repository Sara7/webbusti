<?php

namespace App\Exception;

class BustiNotValidPasswordException extends BustiException
{
    protected $code = BustiException::ERROR_PASSWORD_NOT_VALID;
}
