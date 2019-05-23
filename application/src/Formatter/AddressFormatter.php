<?php

namespace App\Formatter;

use App\Entity\Address;

class AddressFormatter
{
    /**
     * @param Address $address
     *
     * @return array
     */
    public function format(Address $address): array
    {
        return [
            'id'                => $address->getId(),

            'type'              => $address->getType(),
            'street_name'       => $address->getStreetName(),
            'street_number'     => $address->getStreetNumber(),
            'zip'               => $address->getZip(),

            'municipality_id'   => $address->getMunicipality()->getId(),
            'municipality_name' => $address->getMunicipality()->getName(),

            'province_id'       => $address->getMunicipality()->getProvince()->getId(),
            'province_name'     => $address->getMunicipality()->getProvince()->getName(),
            'province_code'     => $address->getMunicipality()->getProvince()->getCode(),

            'country'           => $address->getMunicipality()->getProvince()->getCountry()->getName(),
        ];
    }
}
