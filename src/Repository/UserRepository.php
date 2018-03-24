<?php
namespace App\Repository;

use App\Helpers\Pagination;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getAll($currentPage = 1, $limit = 5)
    {
        $qb = $this->createQueryBuilder('s')->getQuery();
        $paginator = Pagination::paginate($qb, $currentPage, $limit);
        return $paginator;
    }
}