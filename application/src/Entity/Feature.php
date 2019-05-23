<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="feature")
 */
class Feature
{
    public const TYPE_SELECT = 'SELECT';
    public const TYPE_RADIO = 'RADIO';
    public const TYPE_CHECKBOX = 'CHECKBOX';
    public const TYPE_RADIO_CHAINED = 'RADIO_CHAINED';

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function isTypeValid(string $type): bool
    {
        switch ($type) {
            case self::TYPE_SELECT:
            case self::TYPE_RADIO:
            case self::TYPE_CHECKBOX:
            case self::TYPE_RADIO_CHAINED:
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
     * @ORM\Column(type="string", length=11)
     *
     * @var string
     */
    protected $code;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=45)
     *
     * @var string
     */
    protected $descriptionDefault;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $rank;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    protected $mandatory;

    /**
     * @ORM\OneToMany(targetEntity="FeatureValue", mappedBy="feature")
     *
     * @var ArrayCollection<FeatureValue>
     */
    protected $values;

    /**
     * @ORM\OneToMany(targetEntity="FeaturePerCategory", mappedBy="feature")
     *
     * @var ArrayCollection<FeaturePerCategory>
     */
    protected $featuresPerCategory;

    /**
     * @ORM\OneToMany(targetEntity="FeaturePerProduct", mappedBy="feature")
     *
     * @var ArrayCollection<FeaturePerProduct>
     */
    protected $featuresPerProduct;

    /**
     * Feature constructor.
     */
    public function __construct()
    {
        $this->rank = 1;
        $this->mandatory = false;
        $this->values = new ArrayCollection();
        $this->featuresPerCategory = new ArrayCollection();
        $this->featuresPerProduct = new ArrayCollection();
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
     * @return bool
     */
    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    /**
     * @param bool $mandatory
     */
    public function setMandatory(bool $mandatory): void
    {
        $this->mandatory = $mandatory;
    }

    /**
     * @return FeatureValue[]
     */
    public function getValues(): array
    {
        return $this->values->toArray();
    }

    /**
     * @param FeatureValue $value
     */
    public function addValue(FeatureValue $value): void
    {
        $this->values->add($value);
    }

    /**
     * @param FeatureValue $value
     */
    public function removeValue(FeatureValue $value): void
    {
        $this->values->removeElement($value);
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
}
