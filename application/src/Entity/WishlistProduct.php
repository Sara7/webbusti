<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="wishlist_product")
 */
class WishlistProduct
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="wishlistedProducts")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="wishlistedByUsers")
     * @ORM\JoinColumn(name="id_product", referencedColumnName="id")
     *
     * @var Product
     */
    protected $product;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $addedAt;

    /**
     * WishlistProduct constructor.
     */
    public function __construct()
    {
        $this->addedAt = new \DateTime();
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
     * @return \DateTime
     */
    public function getAddedAt(): \DateTime
    {
        return $this->addedAt;
    }

    /**
     * @param \DateTime $addedAt
     */
    public function setAddedAt(\DateTime $addedAt): void
    {
        $this->addedAt = $addedAt;
    }
}
