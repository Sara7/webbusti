<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="province")
 */
class Province
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
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="provinces")
     * @ORM\JoinColumn(name="id_country", referencedColumnName="id", nullable=true)
     *
     * @var Country|null
     */
    protected $country;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=2)
     *
     * @var string|null
     */
    protected $code;

    /**
     * @ORM\OneToMany(targetEntity="Municipality", mappedBy="province")
     *
     * @var ArrayCollection<Municipality>
     */
    protected $municipalities;

    /**
     * Province constructor.
     */
    public function __construct()
    {
        $this->municipalities = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Country|null
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param Country|null $country
     */
    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
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
     * @return string|null
     */
    public function getCode(): ?string
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
     * @return Municipality[]
     */
    public function getMunicipalities(): array
    {
        return $this->municipalities->toArray();
    }

    /**
     * @param Municipality $municipality
     */
    public function addMunicipality(Municipality $municipality): void
    {
        $this->municipalities->add($municipality);
    }

    /**
     * @param Municipality $municipality
     */
    public function removeMunicipality(Municipality $municipality): void
    {
        $this->municipalities->removeElement($municipality);
    }
}
