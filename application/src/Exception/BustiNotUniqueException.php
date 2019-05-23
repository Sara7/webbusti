<?php

namespace App\Exception;

class BustiNotUniqueException extends BustiException
{
    protected $code = BustiException::ERROR_NOT_UNIQUE;
}
