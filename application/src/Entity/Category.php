<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category
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
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="childrenCategories")
     * @ORM\JoinColumn(name="id_parent_category", referencedColumnName="id", nullable=true)
     *
     * @var Category|null
     */
    protected $parentCategory;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $nameDefault;

    /**
     * @ORM\Column(type="string", length=11)
     *
     * @var string
     */
    protected $code;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $level;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @var string
     */
    protected $unit;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     *
     * @var string
     */
    protected $icon;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $rank;

    /**
     * @ORM\Column(type="string", length=7)
     *
     * @var string
     */
    protected $color;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parentCategory")
     *
     * @var ArrayCollection<Category>
     */
    protected $childrenCategories;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="category")
     *
     * @var ArrayCollection<Category>
     */
    protected $products;

    /**
     * @ORM\OneToMany(targetEntity="FeaturePerCategory", mappedBy="category")
     *
     * @var ArrayCollection<FeaturePerCategory>
     */
    protected $featuresPerCategory;

    /**
     * @ORM\OneToMany(targetEntity="MediaPerCategory", mappedBy="category")
     *
     * @var ArrayCollection<MediaPerCategory>
     */
    protected $mediaPerCategory;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->childrenCategories = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->featuresPerCategory = new ArrayCollection();
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
     * @return Category|null
     */
    public function getParentCategory(): ?Category
    {
        return $this->parentCategory;
    }

    /**
     * @param Category|null $parentCategory
     */
    public function setParentCategory(?Category $parentCategory): void
    {
        $this->parentCategory = $parentCategory;
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
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
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
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @return int
     */
    public function getRank(): int
    {
        return $this->rank;
    }

    /**
     * @param int $rank
     */
    public function setRank(int $rank): void
    {
        $this->rank = $rank;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return Category[]
     */
    public function getChildrenCategories(): array
    {
        return $this->childrenCategories->toArray();
    }

    /**
     * @param Category $childrenCategory
     */
    public function addChildrenCategory(Category $childrenCategory): void
    {
        $this->childrenCategories->add($childrenCategory);
    }

    /**
     * @param Category $childrenCategory
     */
    public function removeChildrenCategory(Category $childrenCategory): void
    {
        $this->childrenCategories->removeElement($childrenCategory);
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        return $this->products->toArray();
    }

    /**
     * @param Product $product
     */
    public function addProduct(Product $product): void
    {
        $this->products->add($product);
    }

    /**
     * @param Product $product
     */
    public function removeProduct(Product $product): void
    {
        $this->products->removeElement($product);
    }

    /**
     * @return FeaturePerCategory[]
     */
    public function getFeaturesPerCategory(): array
    {
        return $this->featuresPerCategory->toArray();
    }

    /**
     * @param FeaturePerCategory $featurePerCategory
     */
    public function addFeaturePerCategory(FeaturePerCategory $featurePerCategory): void
    {
        $this->featuresPerCategory->add($featurePerCategory);
    }

    /**
     * @param FeaturePerCategory $featurePerCategory
     */
    public function removeFeaturePerCategory(FeaturePerCategory $featurePerCategory): void
    {
        $this->featuresPerCategory->removeElement($featurePerCategory);
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
