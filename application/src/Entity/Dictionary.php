<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="dictionary")
 */
class Dictionary
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
    protected $descriptionIta;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $descriptionEng;

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
    public function getDescriptionIta(): string
    {
        return $this->descriptionIta;
    }

    /**
     * @param string $descriptionIta
     */
    public function setDescriptionIta(string $descriptionIta): void
    {
        $this->descriptionIta = $descriptionIta;
    }

    /**
     * @return string
     */
    public function getDescriptionEng(): string
    {
        return $this->descriptionEng;
    }

    /**
     * @param string $descriptionEng
     */
    public function setDescriptionEng(string $descriptionEng): void
    {
        $this->descriptionEng = $descriptionEng;
    }
}
