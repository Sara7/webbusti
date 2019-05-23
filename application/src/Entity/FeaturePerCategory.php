<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="feature_per_category")
 */
class FeaturePerCategory
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
     * @ORM\ManyToOne(targetEntity="Feature", inversedBy="featuresPerCategory")
     * @ORM\JoinColumn(name="id_feature", referencedColumnName="id", nullable=true)
     *
     * @var Feature
     */
    protected $feature;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="featuresPerCategory")
     * @ORM\JoinColumn(name="id_category", referencedColumnName="id", nullable=true)
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    protected $active;

    /**
     * FeaturePerCategory constructor.
     */
    public function __construct()
    {
        $this->active = true;
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
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
