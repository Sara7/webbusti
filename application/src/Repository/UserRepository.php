<?php

namespace App\Repository;

use App\Entity\User;
use App\Pager\Page;
use App\Utils\DoctrineQueryBuilderUtil;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class UserRepository
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
     * @return User|null
     */
    public function get(int $id): ?User
    {
        $repository = $this->getRepository();

        /** @var User|null $user */
        $user = $repository->find($id);

        return $user;
    }

    /**
     * @param string $email
     * @param string $code
     *
     * @return User|null
     */
    public function getByActivationCode(string $email, string $code): ?User
    {
        $repository = $this->getRepository();

        /** @var User|null $user */
        $user = $repository->findOneBy([
            'email' => $email,
            'validationCode' => $code,
        ]);

        return $user;
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        $repository = $this->getRepository();

        /** @var User|null $user */
        $user = $repository->findOneBy([
            'email' => $email,
        ]);

        return $user;
    }

    /**
     * @param int $perPage
     * @param int $page
     *
     * @return Page
     */
    public function getUsers($perPage = 10, $page = 1): Page
    {
        $qb = $this->getQueryBuilder()
            ->orderBy('u.createdAt', Criteria::DESC)
        ;

        return $this->queryBuilderUtil->fetchPage($qb, $perPage, $page);
    }

    /**
     * @return User[]
     */
    public function getAdmins(): array
    {
        $repository = $this->getRepository();

        /** @var User[] $admins */
        $admins = $repository->findBy([
            'admin' => true,
        ]);

        return $admins;
    }

    /**
     * @param User $user
     */
    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     */
    public function delete(User $user): void
    {
        $this->entityManager->remove($user);
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
            ->select('u')
            ->from(User::class, 'u')
        ;
    }

    /**
     * @return ObjectRepository
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(User::class);
    }
}
