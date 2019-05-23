<?php

namespace App\Formatter;

use App\Entity\UserQualification;

class UserQualificationFormatter
{
    /**
     * @param UserQualification $userQualification
     *
     * @return array
     */
    public function format(UserQualification $userQualification): array
    {
        return [
            'id'    => $userQualification->getId(),
            'title' => $userQualification->getTitle(),
        ];
    }
}
