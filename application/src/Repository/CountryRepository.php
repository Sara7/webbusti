<?php

namespace App\Repository;

use App\Entity\Country;
use App\Utils\DoctrineQueryBuilderUtil;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class CountryRepository
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
     * @return Country[]
     */
    public function getCountrys(): array
    {
        $qb = $this->getQueryBuilder()
            ->orderBy('c.name', Criteria::ASC)
        ;

        return $this->queryBuilderUtil->fetchAll($qb);
    }

    /**
     * @param int $id
     *
     * @return Country|null
     */
    public function fetch(int $id): ?Country
    {
        /** @var Country|null $country */
        $country = $this->getRepository()->find($id);

        return $country;
    }

    /**
     * @param Country $country
     */
    public function save(Country $country): void
    {
        $this->entityManager->persist($country);
        $this->entityManager->flush();
    }

    /**
     * @param Country $country
     */
    public function delete(Country $country): void
    {
        $this->entityManager->remove($country);
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
            ->select('c')
            ->from(Country::class, 'c')
        ;
    }

    /**
     * @return ObjectRepository
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Country::class);
    }
}
