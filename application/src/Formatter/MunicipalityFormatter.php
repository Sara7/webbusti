<?php

namespace App\Formatter;

use App\Entity\Municipality;

class MunicipalityFormatter
{
    /**
     * @var ProvinceFormatter
     */
    private $provinceFormatter;

    /**
     * MunicipalityFormatter constructor.
     *
     * @param ProvinceFormatter $provinceFormatter
     */
    public function __construct(ProvinceFormatter $provinceFormatter)
    {
        $this->provinceFormatter = $provinceFormatter;
    }

    /**
     * @param Municipality $municipality
     *
     * @return array
     */
    public function format(Municipality $municipality): array
    {
        return [
            'id'   => $municipality->getId(),
            'name' => $municipality->getName(),
            'province' => $this->provinceFormatter->format($municipality->getProvince()),
        ];
    }
}
