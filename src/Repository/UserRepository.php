<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserRepository extends EntityRepository
{
	/**
	 * Intended to be called by PaginationHandler to paginate the query
	 * @return Query a query for all users.
	 */
    public function getAll(): Query
    {
        return $this->createQueryBuilder('u')->getQuery();
    }
}