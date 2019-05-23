<?php

namespace App\Formatter;

use App\Entity\FeatureValue;

class FeatureValueFormatter
{
    /**
     * @param FeatureValue $featureValue
     *
     * @return array
     */
    public function format(FeatureValue $featureValue): array
    {
        return [
            'id'   => $featureValue->getId(),
            'name' => $featureValue->getNameDefault(),
        ];
    }
}
