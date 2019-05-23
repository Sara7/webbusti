<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_item")
 */
class OrderItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int|null
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="orderItems")
     * @ORM\JoinColumn(name="id_item")
     *
     * @var Item
     */
    protected $item;

    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="items")
     * @ORM\JoinColumn(name="id_order")
     *
     * @var Order
     */
    protected $order;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $quantity;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $amountPerUnit;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $amountTotal;

    /**
     * OrderItem constructor.
     */
    public function __construct()
    {
        $this->quantity = 0;
        $this->amountPerUnit = 0;
        $this->amountTotal = 0;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @param Item $item
     */
    public function setItem(Item $item): void
    {
        $this->item = $item;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
        $this->updateAmountTotal();
    }

    /**
     * @return int
     */
    public function getAmountPerUnit(): int
    {
        return $this->amountPerUnit;
    }

    /**
     * @param int $amountPerUnit
     */
    public function setAmountPerUnit(int $amountPerUnit): void
    {
        $this->amountPerUnit = $amountPerUnit;
        $this->updateAmountTotal();
    }

    /**
     * @return int
     */
    public function getAmountTotal(): int
    {
        return $this->amountTotal;
    }

    /**
     * @param int $amountTotal
     */
    public function setAmountTotal(int $amountTotal): void
    {
        $this->amountTotal = $amountTotal;
    }

    private function updateAmountTotal(): void
    {
        $this->amountTotal = $this->amountPerUnit * $this->quantity;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }
}
