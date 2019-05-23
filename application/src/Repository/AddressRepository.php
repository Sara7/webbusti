<?php

namespace App\Repository;

use App\Entity\Address;
use App\Entity\User;
use App\Pager\Page;
use App\Utils\DoctrineQueryBuilderUtil;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class AddressRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var DoctrineQueryBuilderUtil */
    protected $queryBuilderUtil;

    /**
     * AddressRepository constructor.
     *
     * @param EntityManagerInterface   $entityManager
     * @param DoctrineQueryBuilderUtil $queryBuilderUtil
     */
    public function __construct(EntityManagerInterface $entityManager, DoctrineQueryBuilderUtil $queryBuilderUtil)
    {
        $this->entityManager = $entityManager;
        $this->queryBuilderUtil = $queryBuilderUtil;
    }

    /**
     * @param int $perPage
     * @param int $page
     *
     * @return Page
     */
    public function getAddresses($perPage = 10, $page = 1): Page
    {
        $qb = $this->getQueryBuilder()
            ->orderBy('a.createdAt', Criteria::DESC)
        ;

        return $this->queryBuilderUtil->fetchPage($qb, $perPage, $page);
    }

    /**
     * @param User $user
     *
     * @return Address[]
     */
    public function getAddressesByUser(User $user): array
    {
        $qb = $this->getQueryBuilder()
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->orderBy('a.favourite', Criteria::DESC)
        ;

        return $this->queryBuilderUtil->fetchAll($qb);
    }

    /**
     * @param Address $address
     */
    public function save(Address $address): void
    {
        $this->entityManager->persist($address);
        $this->entityManager->flush();
    }

    /**
     * @param Address $address
     */
    public function delete(Address $address): void
    {
        $this->entityManager->remove($address);
        $this->entityManager->flush();
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        return $this
            ->entityManager
            ->createQueryBuilder()
            ->select('a')
            ->from(Address::class, 'a')
        ;
    }

    /**
     * @return ObjectRepository
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Address::class);
    }
}
