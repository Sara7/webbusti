<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="feature_per_product")
 */
class FeaturePerProduct
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
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="featuresPerProduct")
     * @ORM\JoinColumn(name="id_product", referencedColumnName="id")
     *
     * @var Product
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="Feature", inversedBy="featuresPerProduct")
     * @ORM\JoinColumn(name="id_feature", referencedColumnName="id")
     *
     * @var Feature
     */
    protected $feature;

    /**
     * @ORM\ManyToOne(targetEntity="FeatureValue", inversedBy="featuresPerProduct")
     * @ORM\JoinColumn(name="id_value", referencedColumnName="id", nullable=true)
     *
     * @var FeatureValue
     */
    protected $value;

    /**
     * FeaturePerProduct constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @return Feature
     */
    public function getFeature(): Feature
    {
        return $this->feature;
    }

    /**
     * @param Feature $feature
     */
    public function setFeature(Feature $feature): void
    {
        $this->feature = $feature;
    }

    /**
     * @return FeatureValue
     */
    public function getValue(): FeatureValue
    {
        return $this->value;
    }

    /**
     * @param FeatureValue $value
     */
    public function setValue(FeatureValue $value): void
    {
        $this->value = $value;
    }
}
