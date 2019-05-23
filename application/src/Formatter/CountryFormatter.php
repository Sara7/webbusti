<?php

namespace App\Formatter;

use App\Entity\Country;

class CountryFormatter
{
    /**
     * @param Country $country
     *
     * @return array
     */
    public function format(Country $country): array
    {
        return [
            'id'   => $country->getId(),
            'name' => $country->getName(),
            'code' => $country->getCode(),
        ];
    }
}
