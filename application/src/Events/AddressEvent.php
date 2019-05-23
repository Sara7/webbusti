<?php

namespace App\Events;

use App\Entity\Address;
use Symfony\Component\EventDispatcher\Event;

class AddressEvent extends Event
{
    /** @var Address */
    private $address;

    /**
     * AddressEvent constructor.
     *
     * @param Address $address
     */
    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }
}
