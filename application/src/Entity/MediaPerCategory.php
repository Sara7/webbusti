<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="media_per_category")
 */
class MediaPerCategory
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
     * @ORM\ManyToOne(targetEntity="Media", inversedBy="mediaPerCategory")
     * @ORM\JoinColumn(name="id_media", referencedColumnName="id")
     *
     * @var Media
     */
    protected $media;

    /**
     * @ORM\ManyToOne(targetEntity="ImageRole", inversedBy="mediaPerCategory")
     * @ORM\JoinColumn(name="id_role", referencedColumnName="id")
     *
     * @var ImageRole
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="mediaPerCategory")
     * @ORM\JoinColumn(name="id_category", referencedColumnName="id")
     *
     * @var Category
     */
    protected $category;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Media
     */
    public function getMedia(): Media
    {
        return $this->media;
    }

    /**
     * @param Media $media
     */
    public function setMedia(Media $media): void
    {
        $this->media = $media;
    }

    /**
     * @return ImageRole
     */
    public function getRole(): ImageRole
    {
        return $this->role;
    }

    /**
     * @param ImageRole $role
     */
    public function setRole(ImageRole $role): void
    {
        $this->role = $role;
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
}
