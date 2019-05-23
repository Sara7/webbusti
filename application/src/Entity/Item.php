<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="item")
 */
class Item
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
     * @ORM\ManyToOne(targetEntity="Format", inversedBy="items")
     * @ORM\JoinColumn(name="id_format", referencedColumnName="id")
     *
     * @var Format
     */
    protected $format;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="items")
     * @ORM\JoinColumn(name="id_product", referencedColumnName="id")
     *
     * @var Product
     */
    protected $product;

    /**
     * @ORM\Column(type="float")
     *
     * @var float
     */
    protected $weight;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $price;

    /**
     * @ORM\Column(type="float")
     *
     * @var float
     */
    protected $volume;

    /**
     * @ORM\OneToMany(targetEntity="ItemPerCart", mappedBy="item")
     *
     * @var ArrayCollection<ItemPerCart>
     */
    protected $itemsPerCart;

    /**
     * @ORM\OneToMany(targetEntity="OrderItem", mappedBy="item")
     *
     * @var ArrayCollection<OrderItem>
     */
    protected $orderItems;

    /**
     * Item constructor.
     */
    public function __construct()
    {
        $this->itemsPerCart = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Format
     */
    public function getFormat(): Format
    {
        return $this->format;
    }

    /**
     * @param Format $format
     */
    public function setFormat(Format $format): void
    {
        $this->format = $format;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getVolume(): float
    {
        return $this->volume;
    }

    /**
     * @param float $volume
     */
    public function setVolume(float $volume): void
    {
        $this->volume = $volume;
    }

    /**
     * @return ItemPerCart[]
     */
    public function getItemsPerCart(): array
    {
        return $this->itemsPerCart->toArray();
    }

    /**
     * @param ItemPerCart $itemPerCart
     */
    public function addItemPerCart(ItemPerCart $itemPerCart): void
    {
        $this->itemsPerCart->add($itemPerCart);
    }

    /**
     * @param ItemPerCart $itemPerCart
     */
    public function removeItemPerCart(ItemPerCart $itemPerCart): void
    {
        $this->itemsPerCart->removeElement($itemPerCart);
    }

    /**
     * @return Item[]
     */
    public function getOrderItems(): array
    {
        return $this->orderItems->toArray();
    }
}
