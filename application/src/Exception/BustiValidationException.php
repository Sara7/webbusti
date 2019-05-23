<?php

namespace App\Exception;

class BustiValidationException extends BustiException
{
    protected $code = BustiException::ERROR_FORMAT_VALIDATION;
}
