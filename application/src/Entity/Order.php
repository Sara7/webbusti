<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="order")
 */
class Order
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_PAID     = 'paid';
    public const STATUS_SHIPPED  = 'shipped';
    public const STATUS_CANCELED = 'canceled';

    /**
     * @param string $status
     *
     * @return bool
     */
    public static function isStatusValid(string $status): bool
    {
        switch ($status) {
            case self::STATUS_PENDING:
            case self::STATUS_PAID:
            case self::STATUS_SHIPPED:
            case self::STATUS_CANCELED:
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
     * @ORM\ManyToOne(targetEntity="user", inversedBy="orders")
     * @ORM\JoinColumn(name="id_user", nullable=true, onDelete="SET NULL")
     *
     * @var User|null
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="address", inversedBy="billedToOrders")
     * @ORM\JoinColumn(name="id_billing_address", nullable=true, onDelete="SET NULL")
     *
     * @var Address|null
     */
    protected $billingAddress;

    /**
     * @ORM\ManyToOne(targetEntity="municipality", inversedBy="billedToOrders")
     * @ORM\JoinColumn(name="id_billing_address_municipality")
     *
     * @var Municipality
     */
    protected $billingAddressMunicipality;

    /**
     * @ORM\ManyToOne(targetEntity="address", inversedBy="shippedToOrders")
     * @ORM\JoinColumn(name="id_shipment_address", nullable=true, onDelete="SET NULL")
     *
     * @var Address|null
     */
    protected $shipmentAddress;

    /**
     * @ORM\ManyToOne(targetEntity="municipality", inversedBy="shippedToOrders")
     * @ORM\JoinColumn(name="id_shipment_address_municipality")
     *
     * @var Municipality
     */
    protected $shipmentAddressMunicipality;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $billingAddressZip;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $billingAddressStreetName;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $billingAddressStreetNumber;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $billingAddressFreeText;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $shipmentAddressZip;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $shipmentAddressStreetName;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $shipmentAddressStreetNumber;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $shipmentAddressFreeText;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $totalAmount;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $transactionId;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime|null
     */
    protected $paidAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime|null
     */
    protected $shippedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime|null
     */
    protected $canceledAt;

    /**
     * @ORM\OneToMany(targetEntity="OrderItem", mappedBy="order")
     *
     * @var ArrayCollection<OrderItem>
     */
    protected $items;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->status = self::STATUS_PENDING;

        try {
            $this->createdAt  = new \DateTime();
            $this->updatedAt  = new \DateTime();
        } catch (\Exception $exception) {
        }

        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Address|null
     */
    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress;
    }

    /**
     * @param Address $billingAddress
     */
    public function setBillingAddress(Address $billingAddress): void
    {
        $this->billingAddressMunicipality = $billingAddress->getMunicipality();
        $this->billingAddressZip = $billingAddress->getZip();
        $this->billingAddressStreetName = $billingAddress->getStreetName();
        $this->billingAddressStreetNumber = $billingAddress->getStreetNumber();
        $this->billingAddressFreeText = $billingAddress->getFreeText();

        $this->billingAddress = $billingAddress;
    }

    /**
     * @return Municipality
     */
    public function getBillingAddressMunicipality(): Municipality
    {
        return $this->billingAddressMunicipality;
    }

    /**
     * @return string
     */
    public function getBillingAddressZip(): string
    {
        return $this->billingAddressZip;
    }

    /**
     * @return string
     */
    public function getBillingAddressStreetName(): string
    {
        return $this->billingAddressStreetName;
    }

    /**
     * @return string
     */
    public function getBillingAddressStreetNumber(): string
    {
        return $this->billingAddressStreetNumber;
    }

    /**
     * @return string
     */
    public function getBillingAddressFreeText(): string
    {
        return $this->billingAddressFreeText;
    }

    /**
     * @return Address|null
     */
    public function getShipmentAddress(): ?Address
    {
        return $this->shipmentAddress;
    }

    /**
     * @param Address $shipmentAddress
     */
    public function setShipmentAddress(Address $shipmentAddress): void
    {
        $this->shipmentAddressMunicipality = $shipmentAddress->getMunicipality();
        $this->shipmentAddressZip = $shipmentAddress->getZip();
        $this->shipmentAddressStreetName = $shipmentAddress->getStreetName();
        $this->shipmentAddressStreetNumber = $shipmentAddress->getStreetNumber();
        $this->shipmentAddressFreeText = $shipmentAddress->getFreeText();

        $this->shipmentAddress = $shipmentAddress;
    }

    /**
     * @return Municipality
     */
    public function getShipmentAddressMunicipality(): Municipality
    {
        return $this->shipmentAddressMunicipality;
    }

    /**
     * @return string
     */
    public function getShipmentAddressZip(): string
    {
        return $this->shipmentAddressZip;
    }

    /**
     * @return string
     */
    public function getShipmentAddressStreetName(): string
    {
        return $this->shipmentAddressStreetName;
    }

    /**
     * @return string
     */
    public function getShipmentAddressStreetNumber(): string
    {
        return $this->shipmentAddressStreetNumber;
    }

    /**
     * @return string
     */
    public function getShipmentAddressFreeText(): string
    {
        return $this->shipmentAddressFreeText;
    }

    /**
     * @return int
     */
    public function getTotalAmount(): int
    {
        return $this->totalAmount;
    }

    /**
     * @param int $totalAmount
     */
    public function setTotalAmount(int $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     */
    public function setTransactionId(string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setAsUpdatedNow(): void
    {
        try {
            $this->updatedAt = new \DateTime();
        } catch (\Exception $exception) {
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getPaidAt(): ?\DateTime
    {
        return $this->paidAt;
    }

    public function setPaid(): void
    {
        $this->status = self::STATUS_PAID;
        try {
            $this->paidAt = new \DateTime();
        } catch (\Exception $exception) {
        }
        $this->setAsUpdatedNow();
    }

    /**
     * @return \DateTime|null
     */
    public function getShippedAt(): ?\DateTime
    {
        return $this->shippedAt;
    }

    public function setShipped(): void
    {
        $this->status = self::STATUS_SHIPPED;
        try {
            $this->shippedAt = new \DateTime();
        } catch (\Exception $exception) {
        }
        $this->setAsUpdatedNow();
    }

    /**
     * @return \DateTime|null
     */
    public function getCanceledAt(): ?\DateTime
    {
        return $this->canceledAt;
    }

    public function setCanceled(): void
    {
        $this->status = self::STATUS_CANCELED;
        try {
            $this->canceledAt = new \DateTime();
        } catch (\Exception $exception) {
        }
        $this->setAsUpdatedNow();
    }
}
