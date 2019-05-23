<?php

namespace App\Exception;

class BustiDataRequiredException extends BustiException
{
    protected $code = BustiException::ERROR_DATA_REQUIRED;
}
