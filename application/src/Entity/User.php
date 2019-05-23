<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    public const TYPE_PRIVATE  = 'private';
    public const TYPE_BUSINESS = 'business';

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE  = 'active';

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function isTypeValid(string $type): bool
    {
        switch ($type) {
            case self::TYPE_BUSINESS:
            case self::TYPE_PRIVATE:
                return true;
        }

        return false;
    }

    /**
     * @param string $status
     *
     * @return bool
     */
    public static function isStatusValid(string $status): bool
    {
        switch ($status) {
            case self::STATUS_PENDING:
            case self::STATUS_ACTIVE:
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
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="UserQualification", inversedBy="users")
     * @ORM\JoinColumn(name="id_qualification", referencedColumnName="id", nullable=true)
     *
     * @var UserQualification|null
     */
    private $qualification;

    /**
     * @ORM\Column(type="uuid")
     *
     * @var UuidInterface
     */
    private $uuid;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $type;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $companyName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $sdiCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $pecAddress;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $fiscalCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $vat;

    /**
     * @ORM\Column(type="date", nullable=true)
     *
     * @var \DateTime|null
     */
    private $birthDate;

    /**
     * @ORM\Column(type="string", length=190, unique=true)
     *
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string|null
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $validationCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $passwordRecoveryCode;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    private $newsletterEnabled;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    private $promoEnabled;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    private $admin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $recoverPasswordStartedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $activatedAt;

    /**
     * @ORM\OneToMany(targetEntity="Address", mappedBy="user")
     *
     * @var ArrayCollection<Address>
     */
    protected $addresses;

    /**
     * @ORM\OneToMany(targetEntity="WishlistProduct", mappedBy="user")
     *
     * @var ArrayCollection<WishlistProduct>
     */
    protected $wishlistedProducts;

    /**
     * @ORM\OneToMany(targetEntity="ItemPerCart", mappedBy="user")
     *
     * @var ArrayCollection<ItemPerCart>
     */
    protected $itemsPerCart;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="user")
     *
     * @var ArrayCollection<Order>
     */
    protected $orders;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->uuid = Uuid::uuid4();
        $this->type = self::TYPE_PRIVATE;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->status = self::STATUS_PENDING;

        $this->newsletterEnabled = false;
        $this->promoEnabled = false;
        $this->admin = false;

        $this->addresses          = new ArrayCollection();
        $this->wishlistedProducts = new ArrayCollection();
        $this->itemsPerCart       = new ArrayCollection();
        $this->orders             = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return UserQualification|null
     */
    public function getQualification(): ?UserQualification
    {
        return $this->qualification;
    }

    /**
     * @param UserQualification|null $qualification
     */
    public function setQualification(?UserQualification $qualification): void
    {
        $this->qualification = $qualification;
    }

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuid;
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
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string|null
     */
    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    /**
     * @param string|null $companyName
     */
    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * @return string|null
     */
    public function getSdiCode(): ?string
    {
        return $this->sdiCode;
    }

    /**
     * @param string|null $sdiCode
     */
    public function setSdiCode(?string $sdiCode): void
    {
        $this->sdiCode = $sdiCode;
    }

    /**
     * @return string|null
     */
    public function getPecAddress(): ?string
    {
        return $this->pecAddress;
    }

    /**
     * @param string|null $pecAddress
     */
    public function setPecAddress(?string $pecAddress): void
    {
        $this->pecAddress = $pecAddress;
    }

    /**
     * @return string|null
     */
    public function getFiscalCode(): ?string
    {
        return $this->fiscalCode;
    }

    /**
     * @param string|null $fiscalCode
     */
    public function setFiscalCode(?string $fiscalCode): void
    {
        $this->fiscalCode = $fiscalCode;
    }

    /**
     * @return string|null
     */
    public function getVat(): ?string
    {
        return $this->vat;
    }

    /**
     * @param string|null $vat
     */
    public function setVat(?string $vat): void
    {
        $this->vat = $vat;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime|null $birthDate
     */
    public function setBirthDate(?\DateTime $birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @throws RuntimeException
     */
    public function setPassword(string $password): void
    {
        $this->plainPassword = $password;

        $encryptedPassword = password_hash($password, PASSWORD_BCRYPT);
        if (false === $encryptedPassword) {
            throw new RuntimeException('Can\'t set the new password');
        }
        $this->password = $encryptedPassword;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @return null|string
     */
    public function getValidationCode(): ?string
    {
        return $this->validationCode;
    }

    /**
     * @return void
     */
    public function generateValidationCode(): void
    {
        try {
            $this->validationCode = Uuid::uuid4()->toString();
        } catch (\Exception $e) {
            $this->validationCode = 'aaabbbcccdddeee';
        }
    }

    /**
     * @return void
     */
    public function resetValidationCode(): void
    {
        $this->validationCode = null;
    }

    /**
     * @return string|null
     */
    public function getPasswordRecoveryCode(): ?string
    {
        return $this->passwordRecoveryCode;
    }

    /**
     * @return void
     */
    public function generatePasswordRecoveryCode(): void
    {
        try {
            $this->passwordRecoveryCode = Uuid::uuid4()->toString();
            $this->setRecoverPasswordStartedAt(new \DateTime());
        } catch (\Exception $e) {
            $this->passwordRecoveryCode = 'aaabbbcccdddeee';
        }
    }

    /**
     * @return void
     */
    public function resetPasswordRecoveryCode(): void
    {
        $this->passwordRecoveryCode = null;
        $this->setRecoverPasswordStartedAt(null);
    }

    /**
     * @return \DateTime|null
     */
    public function getActivatedAt(): ?\DateTime
    {
        return $this->activatedAt;
    }

    /**
     * @param \DateTime|null $activatedAt
     */
    public function setActivatedAt(?\DateTime $activatedAt): void
    {
        $this->activatedAt = $activatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getRecoverPasswordStartedAt(): ?\DateTime
    {
        return $this->recoverPasswordStartedAt;
    }

    /**
     * @param \DateTime|null $recoverPasswordStartedAt
     */
    public function setRecoverPasswordStartedAt(?\DateTime $recoverPasswordStartedAt): void
    {
        $this->recoverPasswordStartedAt = $recoverPasswordStartedAt;
    }

    /**
     * @return bool
     */
    public function isNewsletterEnabled(): bool
    {
        return $this->newsletterEnabled;
    }

    /**
     * @param bool $newsletterEnabled
     */
    public function setNewsletterEnabled(bool $newsletterEnabled): void
    {
        $this->newsletterEnabled = $newsletterEnabled;
    }

    /**
     * @return bool
     */
    public function isPromoEnabled(): bool
    {
        return $this->promoEnabled;
    }

    /**
     * @param bool $promoEnabled
     */
    public function setPromoEnabled(bool $promoEnabled): void
    {
        $this->promoEnabled = $promoEnabled;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }

    /**
     * @param bool $admin
     */
    public function setAdmin(bool $admin): void
    {
        $this->admin = $admin;
    }

    /**
     * @return Address[]
     */
    public function getAddresses(): array
    {
        return $this->addresses->toArray();
    }

    /**
     * @param Address $address
     */
    public function addAddress(Address $address): void
    {
        $this->addresses->add($address);
    }

    /**
     * @param Address $address
     */
    public function removeAddress(Address $address): void
    {
        $this->addresses->removeElement($address);
    }

    /**
     * @return WishlistProduct[]
     */
    public function getWishlistedProducts(): array
    {
        return $this->wishlistedProducts->toArray();
    }

    /**
     * @param WishlistProduct $wishlistedProduct
     */
    public function addWishlistedProduct(WishlistProduct $wishlistedProduct): void
    {
        $this->wishlistedProducts->add($wishlistedProduct);
    }

    /**
     * @param WishlistProduct $wishlistedProduct
     */
    public function removeWishlistedProduct(WishlistProduct $wishlistedProduct): void
    {
        $this->wishlistedProducts->removeElement($wishlistedProduct);
    }

    /**
     * @return ItemPerCart[]
     */
    public function getItemsPerCart(): array
    {
        return $this->itemsPerCart->toArray();
    }

    /**
     * @param ItemPerCart $itemPerCart
     */
    public function addItemPerCart(ItemPerCart $itemPerCart): void
    {
        $this->itemsPerCart->add($itemPerCart);
    }

    /**
     * @param ItemPerCart $itemPerCart
     */
    public function removeItemPerCart(ItemPerCart $itemPerCart): void
    {
        $this->itemsPerCart->removeElement($itemPerCart);
    }

    /**
     * @return Order[]
     */
    public function getOrders(): array
    {
        return $this->orders->toArray();
    }

    /**
     * @param Order $order
     */
    public function addOrder(Order $order): void
    {
        $order->setUser($this);

        $this->orders->add($order);
    }

    /**
     * @param Order $order
     */
    public function removeOrder(Order $order): void
    {
        $this->orders->removeElement($order);
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return array('ROLE_USER');
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles(): array
    {
        $roles = [];

        if ($this->isAdmin()) {
            $roles[] = new Role('ROLE_ADMIN');
        } else {
            $roles[] = new Role('ROLE_USER');
        }

        return $roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(): string
    {
        return $this->getEmail();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @return array
     */
    public function getMeSerialized(): array
    {
        return [
            'id'    => $this->getId(),
            'email' => $this->getEmail(),
        ];
    }
}
