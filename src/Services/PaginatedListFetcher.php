<?php
/**
 * Created by PhpStorm.
 * User: martynas
 * Date: 18.3.23
 * Time: 22.23
 */

namespace App\Services;


use Doctrine\ORM\EntityManagerInterface;

class PaginatedListFetcher
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getPaginatedList($className, $page = 1, $limit = 5)
    {
        $repository = $this->em->getRepository($className);
        $results = $repository->getAll($page, $limit);

        $pageCount = ceil($results->count() / $limit);

        return ['items' => $results,
            'pageCount' => $pageCount,
            'currentPage' => $page,
            'totalCount' => $results->count()];
    }

}