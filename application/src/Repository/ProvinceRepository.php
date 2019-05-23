<?php

namespace App\Repository;

use App\Entity\Province;
use App\Pager\Page;
use App\Utils\DoctrineQueryBuilderUtil;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class ProvinceRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var DoctrineQueryBuilderUtil */
    protected $queryBuilderUtil;

    /**
     * ProvinceRepository constructor.
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
    public function getProvinces($perPage = 10, $page = 1): Page
    {
        $qb = $this->getQueryBuilder()
            ->orderBy('p.name', Criteria::ASC)
        ;

        return $this->queryBuilderUtil->fetchPage($qb, $perPage, $page);
    }

    /**
     * @return Province[]
     */
    public function getAllProvinces(): array
    {
        $qb = $this->getQueryBuilder()
            ->orderBy('p.name', Criteria::ASC)
        ;

        return $this->queryBuilderUtil->fetchAll($qb);
    }

    /**
     * @param int $id
     *
     * @return Province|null
     */
    public function fetch(int $id): ?Province
    {
        /** @var Province|null $province */
        $province = $this->getRepository()->find($id);

        return $province;
    }

    /**
     * @param Province $province
     */
    public function save(Province $province): void
    {
        $this->entityManager->persist($province);
        $this->entityManager->flush();
    }

    /**
     * @param Province $province
     */
    public function delete(Province $province): void
    {
        $this->entityManager->remove($province);
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
            ->select('p')
            ->from(Province::class, 'p')
        ;
    }

    /**
     * @return ObjectRepository
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Province::class);
    }
}
