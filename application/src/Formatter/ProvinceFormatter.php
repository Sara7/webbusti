<?php

namespace App\Formatter;

use App\Entity\Province;

class ProvinceFormatter
{
    /**
     * @var CountryFormatter
     */
    private $countryFormatter;

    /**
     * ProvinceFormatter constructor.
     *
     * @param CountryFormatter $countryFormatter
     */
    public function __construct(CountryFormatter $countryFormatter)
    {
        $this->countryFormatter = $countryFormatter;
    }

    /**
     * @param Province $province
     *
     * @return array
     */
    public function format(Province $province): array
    {
        return [
            'id'   => $province->getId(),
            'name' => $province->getName(),
            'code' => $province->getCode(),

            'country' => $this->countryFormatter->format($province->getCountry()),
        ];
    }
}
