<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="municipality")
 */
class Municipality
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Province", inversedBy="municipalities")
     * @ORM\JoinColumn(name="id_province", referencedColumnName="id", nullable=true)
     *
     * @var Province|null
     */
    protected $province;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Address", mappedBy="municipality")
     *
     * @var ArrayCollection<Address>
     */
    protected $addresses;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="billingAddressMunicipality")
     *
     * @var ArrayCollection<Order>
     */
    protected $billedToOrders;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="shipmentAddressMunicipality")
     *
     * @var ArrayCollection<Order>
     */
    protected $shippedToOrders;

    /**
     * Municipality constructor.
     */
    public function __construct()
    {
        $this->addresses = new ArrayCollection();
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
     * @return Province|null
     */
    public function getProvince(): ?Province
    {
        return $this->province;
    }

    /**
     * @param Province|null $province
     */
    public function setProvince(?Province $province): void
    {
        $this->province = $province;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Address[]
     */
    public function getAddresses(): array
    {
        return $this->addresses->toArray();
    }

    /**
     * @param Address $address
     */
    public function addAddress(Address $address): void
    {
        $this->addresses->add($address);
    }

    /**
     * @param Address $address
     */
    public function removeAddress(Address $address): void
    {
        $this->addresses->removeElement($address);
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
