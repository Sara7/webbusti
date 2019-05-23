<?php

namespace App\Formatter;

use App\Entity\User;

class UserFormatter
{
    /**
     * @var UserQualificationFormatter
     */
    private $userQualificationFormatter;

    /**
     * UserFormatter constructor.
     *
     * @param UserQualificationFormatter $userQualificationFormatter
     */
    public function __construct(UserQualificationFormatter $userQualificationFormatter)
    {
        $this->userQualificationFormatter = $userQualificationFormatter;
    }

    /**
     * @param User $user
     * @param User|null $for
     *
     * @return array
     */
    public function format(User $user, ?User $for = null): array
    {
        if (null === $for) {
            $for = $user;
        }

        $userDetails = [
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
        ];

        if ($for->isAdmin() || $for->getId() === $user->getId()) {
            $userDetails['type'] = $user->getType();
            $userDetails['promo_enabled'] = $user->isPromoEnabled();
            $userDetails['newsletter_enabled'] = $user->isNewsletterEnabled();
            $userDetails['status'] = $user->getStatus();
        }

        return $userDetails;
    }

    /**
     * @param User      $user
     * @param User|null $for
     *
     * @return array
     */
    public function formatFull(User $user, ?User $for = null): array
    {
        if (null === $for) {
            $for = $user;
        }

        $userDetails = $this->format($user, $for);

        if ($for->isAdmin() || $for->getId() === $user->getId()) {
            $userDetails['firstname'] = $user->getFirstname();
            $userDetails['lastname'] = $user->getLastname();
            $userDetails['company_name'] = $user->getCompanyName();
            $userDetails['birth_date'] = $user->getBirthDate();
            $userDetails['sdi_code'] = $user->getSdiCode();
            $userDetails['pec_address'] = $user->getPecAddress();
            $userDetails['fiscal_code'] = $user->getFiscalCode();
            $userDetails['vat'] = $user->getVat();
            $userDetails['phone_number'] = $user->getPhoneNumber();
            $userDetails['qualification'] = $this->userQualificationFormatter->format($user->getQualification());
        }

        return $userDetails;
    }
}
