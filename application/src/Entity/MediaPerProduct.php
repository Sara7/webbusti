<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="media_per_product")
 */
class MediaPerProduct
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
     * @ORM\ManyToOne(targetEntity="Media", inversedBy="mediaPerProduct")
     * @ORM\JoinColumn(name="id_media", referencedColumnName="id")
     *
     * @var Media
     */
    protected $media;

    /**
     * @ORM\ManyToOne(targetEntity="ImageRole", inversedBy="mediaPerProduct")
     * @ORM\JoinColumn(name="id_role", referencedColumnName="id")
     *
     * @var ImageRole
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="mediaPerProduct")
     * @ORM\JoinColumn(name="id_product", referencedColumnName="id")
     *
     * @var Product
     */
    protected $product;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Media
     */
    public function getMedia(): Media
    {
        return $this->media;
    }

    /**
     * @param Media $media
     */
    public function setMedia(Media $media): void
    {
        $this->media = $media;
    }

    /**
     * @return ImageRole
     */
    public function getRole(): ImageRole
    {
        return $this->role;
    }

    /**
     * @param ImageRole $role
     */
    public function setRole(ImageRole $role): void
    {
        $this->role = $role;
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
}
