<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="country")
 */
class Country
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
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=3)
     *
     * @var string
     */
    protected $code;

    /**
     * @ORM\OneToMany(targetEntity="Province", mappedBy="country")
     *
     * @var ArrayCollection<Province>
     */
    protected $provinces;

    /**
     * Country constructor.
     */
    public function __construct()
    {
        $this->provinces = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     * @return Province[]
     */
    public function getProvinces(): array
    {
        return $this->provinces->toArray();
    }

    /**
     * @param Province $province
     */
    public function addProvince(Province $province): void
    {
        $this->provinces->add($province);
    }

    /**
     * @param Province $province
     */
    public function removeProvince(Province $province): void
    {
        $this->provinces->removeElement($province);
    }
}
