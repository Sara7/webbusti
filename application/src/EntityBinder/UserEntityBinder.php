<?php

namespace App\EntityBinder;

use App\Entity\User;
use App\Exception\BustiDataRequiredException;
use Symfony\Component\HttpFoundation\Request;

class UserEntityBinder
{
    /**
     * @param Request $request
     *
     * @return User
     *
     * @throws BustiDataRequiredException
     */
    public function bindRequestForCreation(Request $request): User
    {
        $user = new User();

        $user->setFirstname($this->validateName($request->request->get('user_firstname')));
        $user->setLastname($this->validateLastname($request->request->get('user_lastname')));

        return $user;
    }

    /**
     * @param string|null $name
     *
     * @return string
     *
     * @throws BustiDataRequiredException
     */
    private function validateName(?string $name): string
    {
        if (!$name) {
            throw new BustiDataRequiredException('Il nome è obbligatorio.');
        }

        return $name;
    }

    /**
     * @param string|null $name
     *
     * @return string
     *
     * @throws BustiDataRequiredException
     */
    private function validateLastname(?string $name): string
    {
        if (!$name) {
            throw new BustiDataRequiredException('Il cognome è obbligatorio.');
        }

        return $name;
    }
}
