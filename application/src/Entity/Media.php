<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="media")
 */
class Media
{
    public const FORMAT_JPG  = 'jpg';
    public const FORMAT_JPEG = 'jpeg';
    public const FORMAT_PNG  = 'png';
    public const FORMAT_GIF  = 'gif';

    /**
     * @param string $format
     *
     * @return bool
     */
    public static function isFormatValid(string $format): bool
    {
        switch ($format) {
            case self::FORMAT_JPG:
            case self::FORMAT_JPEG:
            case self::FORMAT_GIF:
            case self::FORMAT_PNG:
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
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $url;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $thumbUrl;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $format;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="MediaPerProduct", mappedBy="media")
     *
     * @var ArrayCollection<MediaPerProduct>
     */
    protected $mediaPerProduct;

    /**
     * @ORM\OneToMany(targetEntity="MediaPerCategory", mappedBy="media")
     *
     * @var ArrayCollection<MediaPerCategory>
     */
    protected $mediaPerCategory;

    /**
     * Media constructor.
     */
    public function __construct()
    {
        $this->mediaPerProduct = new ArrayCollection();
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
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getThumbUrl(): string
    {
        return $this->thumbUrl;
    }

    /**
     * @param string $thumbUrl
     */
    public function setThumbUrl(string $thumbUrl): void
    {
        $this->thumbUrl = $thumbUrl;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return MediaPerProduct[]
     */
    public function getMediaPerProduct(): array
    {
        return $this->mediaPerProduct->toArray();
    }

    /**
     * @param MediaPerProduct $mediaPerProduct
     */
    public function addMediaPerProduct(MediaPerProduct $mediaPerProduct): void
    {
        $this->mediaPerProduct->add($mediaPerProduct);
    }

    /**
     * @param MediaPerProduct $mediaPerProduct
     */
    public function removeMediaPerProduct(MediaPerProduct $mediaPerProduct): void
    {
        $this->mediaPerProduct->removeElement($mediaPerProduct);
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
