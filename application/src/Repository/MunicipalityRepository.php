<?php

namespace App\Repository;

use App\Entity\Municipality;
use App\Entity\Province;
use App\Pager\Page;
use App\Utils\DoctrineQueryBuilderUtil;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class MunicipalityRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var DoctrineQueryBuilderUtil */
    protected $queryBuilderUtil;

    /**
     * MunicipalityRepository constructor.
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
    public function getMunicipalities($perPage = 10, $page = 1): Page
    {
        $qb = $this->getQueryBuilder()
            ->orderBy('m.name', Criteria::ASC)
        ;

        return $this->queryBuilderUtil->fetchPage($qb, $perPage, $page);
    }

    /**
     * @return Municipality[]
     */
    public function getAllMunicipalities(): array
    {
        $qb = $this->getQueryBuilder()
            ->orderBy('m.name', Criteria::ASC)
        ;

        return $this->queryBuilderUtil->fetchAll($qb);
    }

    /**
     * @param Province $province
     * @param int      $perPage
     * @param int      $page
     *
     * @return Page
     */
    public function getMunicipalitiesByProvince(Province $province, $perPage = 10, $page = 1): Page
    {
        $qb = $this->getQueryBuilder()
            ->andWhere('m.province = :province')
            ->setParameter('province', $province)
            ->orderBy('m.name', Criteria::ASC)
        ;

        return $this->queryBuilderUtil->fetchPage($qb, $perPage, $page);
    }

    /**
     * @param Province $province
     *
     * @return Municipality[]
     */
    public function getAllMunicipalitiesByProvince(Province $province): array
    {
        $qb = $this->getQueryBuilder()
            ->andWhere('m.province = :province')
            ->setParameter('province', $province)
            ->orderBy('m.name', Criteria::ASC)
        ;

        return $this->queryBuilderUtil->fetchAll($qb);
    }

    /**
     * @param int $id
     *
     * @return Municipality
     */
    public function fetch(int $id): Municipality
    {
        /** @var Municipality|null $municipality */
        $municipality = $this->getRepository()->find($id);

        return $municipality;
    }

    /**
     * @param Municipality $municipality
     */
    public function save(Municipality $municipality): void
    {
        $this->entityManager->persist($municipality);
        $this->entityManager->flush();
    }

    /**
     * @param Municipality $municipality
     */
    public function delete(Municipality $municipality): void
    {
        $this->entityManager->remove($municipality);
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
            ->select('m')
            ->from(Municipality::class, 'm')
        ;
    }

    /**
     * @return ObjectRepository
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Municipality::class);
    }
}
