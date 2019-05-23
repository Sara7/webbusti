<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="address")
 */
class Address
{
    public const TYPE_REGISTERED_OFFICE = 'registered_office';
    public const TYPE_DELIVERY          = 'delivery';

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function isTypeValid(string $type): bool
    {
        switch ($type) {
            case self::TYPE_REGISTERED_OFFICE:
            case self::TYPE_DELIVERY:
                return true;
        }

        return false;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="addresses", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Municipality", inversedBy="addresses")
     * @ORM\JoinColumn(name="id_municipality", referencedColumnName="id")
     *
     * @var Municipality
     */
    protected $municipality;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=5)
     *
     * @var string
     */
    protected $zip;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $streetName;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $streetNumber;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $freeText;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    protected $favourite;

    /**
     * @ORM\OneToMany(targetEntity="order", mappedBy="billingAddress")
     *
     * @var ArrayCollection<Order>
     */
    protected $billedToOrders;

    /**
     * @ORM\OneToMany(targetEntity="order", mappedBy="shipmentAddress")
     *
     * @var ArrayCollection<Order>
     */
    protected $shippedToOrders;

    /**
     * Address constructor.
     */
    public function __construct()
    {
        $this->type      = self::TYPE_DELIVERY;
        $this->favourite = false;
        $this->billedToOrders = new ArrayCollection();
        $this->shippedToOrders = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Municipality
     */
    public function getMunicipality(): Municipality
    {
        return $this->municipality;
    }

    /**
     * @param Municipality $municipality
     */
    public function setMunicipality(Municipality $municipality): void
    {
        $this->municipality = $municipality;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getStreetName(): string
    {
        return $this->streetName;
    }

    /**
     * @param string $streetName
     */
    public function setStreetName(string $streetName): void
    {
        $this->streetName = $streetName;
    }

    /**
     * @return string
     */
    public function getStreetNumber(): string
    {
        return $this->streetNumber;
    }

    /**
     * @param string $streetNumber
     */
    public function setStreetNumber(string $streetNumber): void
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return string
     */
    public function getFreeText(): string
    {
        return $this->freeText;
    }

    /**
     * @param string $freeText
     */
    public function setFreeText(string $freeText): void
    {
        $this->freeText = $freeText;
    }

    /**
     * @return bool
     */
    public function isFavourite(): bool
    {
        return $this->favourite;
    }

    /**
     * @param bool $favourite
     */
    public function setFavourite(bool $favourite): void
    {
        $this->favourite = $favourite;
    }

    /**
     * @return Order[]
     */
    public function getBilledToOrders(): array
    {
        return $this->billedToOrders->toArray();
    }

    /**
     * @param Order $order
     */
    public function addBilledToOrder(Order $order): void
    {
        $this->billedToOrders->add($order);
    }

    /**
     * @param Order $order
     */
    public function removeBilledToOrder(Order $order): void
    {
        $this->billedToOrders->removeElement($order);
    }

    /**
     * @return Order[]
     */
    public function getShippedToOrders(): array
    {
        return $this->shippedToOrders->toArray();
    }

    /**
     * @param Order $order
     */
    public function addShippedToOrder(Order $order): void
    {
        $this->shippedToOrders->add($order);
    }

    /**
     * @param Order $order
     */
    public function removeShippedToOrder(Order $order): void
    {
        $this->shippedToOrders->removeElement($order);
    }
}
