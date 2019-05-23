<?php

namespace App\Utils;

use App\Pager\Page;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DoctrineQueryBuilderUtil
{
    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return array
     */
    public function fetchAll(QueryBuilder $queryBuilder): array
    {
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param int          $perPage
     * @param int          $page
     *
     * @return Page
     */
    public function fetchPage(QueryBuilder $queryBuilder, int $perPage = 10, int $page = 1): Page
    {
        $query = $queryBuilder->getQuery();

        $paginator = new Paginator($query);

        $totalItems = count($paginator);
        $pagesCount = (int) ceil($totalItems / $perPage);

        $page = min(max($page, 1), $pagesCount);

        $results = array();
        $paginator
            ->getQuery()
            ->setFirstResult($perPage * ($page - 1))
            ->setMaxResults($perPage)
        ;

        foreach ($paginator as $item) {
            $results[] = $item;
        }

        return new Page($results, $perPage, $totalItems, $page);
    }
}
