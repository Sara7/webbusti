<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="feature_value")
 */
class FeatureValue
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
     * @ORM\ManyToOne(targetEntity="Feature", inversedBy="values")
     * @ORM\JoinColumn(name="id_feature", referencedColumnName="id")
     *
     * @var Feature
     */
    protected $feature;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $nameDefault;

    /**
     * @ORM\OneToMany(targetEntity="FeaturePerProduct", mappedBy="value", cascade={"persist", "remove"})
     *
     * @var ArrayCollection<FeaturePerProduct>
     */
    protected $featuresPerProduct;

    /**
     * FeatureValue constructor.
     */
    public function __construct()
    {
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
