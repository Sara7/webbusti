<?php

namespace App\Formatter;

use App\Entity\Feature;

class FeatureFormatter
{
    /** @var FeatureValueFormatter */
    protected $valueFormatter;

    /**
     * FeatureFormatter constructor.
     *
     * @param FeatureValueFormatter $valueFormatter
     */
    public function __construct(FeatureValueFormatter $valueFormatter)
    {
        $this->valueFormatter = $valueFormatter;
    }

    /**
     * @param Feature $feature
     *
     * @return array
     */
    public function format(Feature $feature): array
    {
        $featureFormatted = [
            'feature_id'                  => $feature->getId(),
            'feature_order'               => $feature->getRank(),
            'feature_code'                => $feature->getCode(),
            'feature_type'                => $feature->getType(),
            'feature_description_default' => $feature->getDescriptionDefault(),
            'feature_values'              => [],
        ];

        $values = $feature->getValues();
        foreach ($values as $value) {
            $featureFormatted['feature_values'][] = $this->valueFormatter->format($value);
        }

        return $featureFormatted;
    }
}
