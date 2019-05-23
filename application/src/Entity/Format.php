<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="format")
 */
class Format
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
     * @ORM\ManyToOne(targetEntity="Dictionary")
     * @ORM\JoinColumn(name="id_description", referencedColumnName="id", nullable=true)
     *
     * @var Dictionary
     */
    protected $description;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $descriptionDefault;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="format")
     *
     * @var ArrayCollection<Item>
     */
    protected $items;

    /**
     * Format constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Dictionary
     */
    public function getDescription(): Dictionary
    {
        return $this->description;
    }

    /**
     * @param Dictionary $description
     */
    public function setDescription(Dictionary $description): void
    {
        $this->description = $description;
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
}
