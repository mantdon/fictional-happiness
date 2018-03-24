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
        $services = $repository->getAll($page, $limit);

        $pageCount = ceil($services->count() / $limit);

        return ['services' => $services,
            'pageCount' => $pageCount,
            'currentPage' => $page];
    }

}