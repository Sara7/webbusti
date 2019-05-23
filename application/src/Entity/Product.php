<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product")
 */
class Product
{
    public const VISIBILITY_NOT_VISIBLE = 1;
    public const VISIBILITY_BUSTI_STORE = 2;
    public const VISIBILITY_WEB_SITE    = 4;
    public const VISIBILITY_APPLICATION = 8;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="id_category", referencedColumnName="id")
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @var string
     */
    protected $code;

    /**
     * @ORM\Column(type="string", length=45)
     *
     * @var string
     */
    protected $nameDefault;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    protected $descriptionDefault;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @var string
     */
    protected $unit;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    protected $shelfLife;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $availability;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $visibility;

    /**
     * @ORM\OneToMany(targetEntity="WishlistProduct", mappedBy="product")
     *
     * @var ArrayCollection<WishlistProduct>
     */
    protected $wishlistedByUsers;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="product")
     *
     * @var ArrayCollection<Item>
     */
    protected $items;

    /**
     * @ORM\OneToMany(targetEntity="Discount", mappedBy="product")
     *
     * @var ArrayCollection<Discount>
     */
    protected $discounts;

    /**
     * @ORM\OneToMany(targetEntity="FeaturePerProduct", mappedBy="product")
     *
     * @var ArrayCollection<FeaturePerProduct>
     */
    protected $featuresPerProduct;

    /**
     * @ORM\OneToMany(targetEntity="MediaPerProduct", mappedBy="product")
     *
     * @var ArrayCollection<MediaPerProduct>
     */
    protected $mediaPerProduct;

    /**
     * @ORM\OneToOne(targetEntity="FeaturedProduct", mappedBy="product")
     *
     * @var FeaturedProduct
     */
    protected $featuredProduct;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="pairedByProducts")
     *
     * @var ArrayCollection<Product>
     */
    protected $pairedProducts;

    /**
     * @ORM\ManyToMany(targetEntity="Product", inversedBy="pairedProducts")
     *
     * @var ArrayCollection<Product>
     */
    protected $pairedByProducts;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->wishlistedByUsers = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->featuresPerProduct = new ArrayCollection();
        $this->mediaPerProduct = new ArrayCollection();
        $this->pairedProducts = new ArrayCollection();
        $this->pairedByProducts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getNameDefault(): string
    {
        return $this->nameDefault;
    }

    /**
     * @param string $nameDefault
     */
    public function setNameDefault(string $nameDefault): void
    {
        $this->nameDefault = $nameDefault;
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
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    /**
     * @return int
     */
    public function getShelfLife(): int
    {
        return $this->shelfLife;
    }

    /**
     * @param int $shelfLife
     */
    public function setShelfLife(int $shelfLife): void
    {
        $this->shelfLife = $shelfLife;
    }

    /**
     * @return string
     */
    public function getAvailability(): string
    {
        return $this->availability;
    }

    /**
     * @param string $availability
     */
    public function setAvailability(string $availability): void
    {
        $this->availability = $availability;
    }

    /**
     * @return int
     */
    public function getVisibility(): int
    {
        return $this->visibility;
    }

    /**
     * @param int $visibility
     */
    public function setVisibility(int $visibility): void
    {
        $this->visibility = $visibility;
    }

    /**
     * @return WishlistProduct[]
     */
    public function getWishlistedByUser(): array
    {
        return $this->wishlistedByUsers->toArray();
    }

    /**
     * @param WishlistProduct $wishlistedByUser
     */
    public function addWishlistedByUser(WishlistProduct $wishlistedByUser): void
    {
        $this->wishlistedByUsers->add($wishlistedByUser);
    }

    /**
     * @param WishlistProduct $wishlistedByUser
     */
    public function removeWishlistedByUser(WishlistProduct $wishlistedByUser): void
    {
        $this->wishlistedByUsers->removeElement($wishlistedByUser);
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items->toArray();
    }

    /**
     * @param Item $item
     */
    public function addItem(Item $item): void
    {
        $this->items->add($item);
    }

    /**
     * @param Item $item
     */
    public function removeItem(Item $item): void
    {
        $this->items->removeElement($item);
    }

    /**
     * @return Discount[]
     */
    public function getDiscounts(): array
    {
        return $this->discounts->toArray();
    }

    /**
     * @param Discount $discount
     */
    public function addDiscount(Discount $discount): void
    {
        $this->discounts->add($discount);
    }

    /**
     * @param Discount $discount
     */
    public function removeDiscount(Discount $discount): void
    {
        $this->discounts->removeElement($discount);
    }

    /**
     * @return FeaturePerProduct[]
     */
    public function getFeaturesPerProduct(): array
    {
        return $this->featuresPerProduct->toArray();
    }

    /**
     * @param FeaturePerProduct $featurePerProduct
     */
    public function addFeaturePerProduct(FeaturePerProduct $featurePerProduct): void
    {
        $this->featuresPerProduct->add($featurePerProduct);
    }

    /**
     * @param FeaturePerProduct $featurePerProduct
     */
    public function removeFeaturePerProduct(FeaturePerProduct $featurePerProduct): void
    {
        $this->featuresPerProduct->removeElement($featurePerProduct);
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
     * @return FeaturedProduct
     */
    public function getFeaturedProduct(): FeaturedProduct
    {
        return $this->featuredProduct;
    }

    /**
     * @param FeaturedProduct $featuredProduct
     */
    public function setFeaturedProduct(FeaturedProduct $featuredProduct): void
    {
        $this->featuredProduct = $featuredProduct;
    }

    /**
     * @return Product[]
     */
    public function getPairedProducts(): array
    {
        return $this->pairedProducts->toArray();
    }

    /**
     * @param Product $product
     */
    public function addPairedProduct(Product $product): void
    {
        $this->pairedProducts->add($product);
    }

    /**
     * @param Product $product
     */
    public function removePairedProduct(Product $product): void
    {
        $this->pairedProducts->removeElement($product);
    }

    /**
     * @return Product[]
     */
    public function getPairedByProducts(): array
    {
        return $this->pairedByProducts->toArray();
    }

    /**
     * @param Product $product
     */
    public function addPairedByProduct(Product $product): void
    {
        $this->pairedByProducts->add($product);
    }

    /**
     * @param Product $product
     */
    public function removePairedByProduct(Product $product): void
    {
        $this->pairedByProducts->removeElement($product);
    }
}
