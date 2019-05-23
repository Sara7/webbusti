<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="image_role")
 */
class ImageRole
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
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $descriptionDefault;

    /**
     * @ORM\OneToMany(targetEntity="MediaPerProduct", mappedBy="role")
     *
     * @var ArrayCollection<MediaPerProduct>
     */
    protected $mediaPerProduct;

    /**
     * @ORM\OneToMany(targetEntity="MediaPerCategory", mappedBy="role")
     *
     * @var ArrayCollection<MediaPerCategory>
     */
    protected $mediaPerCategory;

    /**
     * ImageRole constructor.
     */
    public function __construct()
    {
        $this->mediaPerProduct = new ArrayCollection();
        $this->mediaPerCategory = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescriptionDefault(): string
    {
        return $this->descriptionDefault;
    }

    /**
     * @param string $descriptionDefault
     */
    public function setDescriptionDefault(string $descriptionDefault): void
    {
        $this->descriptionDefault = $descriptionDefault;
    }

    /**
     * @return MediaPerProduct[]
     */
    public function getMediaPerProduct(): array
    {
        return $this->mediaPerProduct->toArray();
    }

    /**
     * @param MediaPerProduct $mediaPerProduct
     */
    public function addMediaPerProduct(MediaPerProduct $mediaPerProduct): void
    {
        $this->mediaPerProduct->add($mediaPerProduct);
    }

    /**
     * @param MediaPerProduct $mediaPerProduct
     */
    public function removeMediaPerProduct(MediaPerProduct $mediaPerProduct): void
    {
        $this->mediaPerProduct->removeElement($mediaPerProduct);
    }

    /**
     * @return MediaPerCategory[]
     */
    public function getMediaPerCategory(): array
    {
        return $this->mediaPerCategory->toArray();
    }

    /**
     * @param MediaPerCategory $mediaPerCategory
     */
    public function addMediaPerCategory(MediaPerCategory $mediaPerCategory): void
    {
        $this->mediaPerCategory->add($mediaPerCategory);
    }

    /**
     * @param MediaPerCategory $mediaPerCategory
     */
    public function removeMediaPerCategory(MediaPerCategory $mediaPerCategory): void
    {
        $this->mediaPerCategory->removeElement($mediaPerCategory);
    }
}
