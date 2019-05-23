<?php

namespace App\Repository;

use App\Entity\UserQualification;
use App\Utils\DoctrineQueryBuilderUtil;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class UserQualificationRepository
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
     * @param int $id
     *
     * @return UserQualification|null
     */
    public function fetch(int $id): ?UserQualification
    {
        /** @var UserQualification|null $userQualification */
        $userQualification = $this->getRepository()->find($id);

        return $userQualification;
    }

    /**
     * @return UserQualification[]
     */
    public function getUserQualifications(): array
    {
        $qb = $this->getQueryBuilder()
            ->orderBy('q.title', Criteria::ASC)
        ;

        return $this->queryBuilderUtil->fetchAll($qb);
    }

    /**
     * @param UserQualification $userQualification
     */
    public function save(UserQualification $userQualification): void
    {
        $this->entityManager->persist($userQualification);
        $this->entityManager->flush();
    }

    /**
     * @param UserQualification $userQualification
     */
    public function delete(UserQualification $userQualification): void
    {
        $this->entityManager->remove($userQualification);
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
            ->select('q')
            ->from(UserQualification::class, 'q')
        ;
    }

    /**
     * @return ObjectRepository
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(UserQualification::class);
    }
}
