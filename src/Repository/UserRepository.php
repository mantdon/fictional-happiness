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

    public function findByPattern(string $pattern): Query
    {
        return $this->createQueryBuilder('u')
            ->where('u.id LIKE :pattern')
            ->orWhere('u.email LIKE :pattern')
            ->orWhere('u.first_name LIKE :pattern')
            ->orWhere('u.last_name LIKE :pattern')
            ->orWhere('u.phone LIKE :pattern')
            ->orWhere('u.address LIKE :pattern')
            ->orWhere('u.city LIKE :pattern')
            ->setParameter('pattern', '%' . $pattern . '%')
            ->getQuery();
    }
}