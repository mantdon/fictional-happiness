<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class UserRepository extends EntityRepository
{
    /**
	 * Intended to be called by PaginationHandler to paginate the query
     * @param null|array $orderBy Array containing key value pairs where key = column, value = order
	 * @return Query a query for all users.
	 */
    public function getAll(?array $orderBy = []): Query
    {
        $query = $this->createQueryBuilder('u');
        $query = $this->addOrders($query, $orderBy);

        return $query->getQuery();
    }

    /**
     * Intended to be called by PaginationHandler to paginate the query
     * @param array $criteria [field => value, ...]
     * @return Query with users that meets defined criteria.
     */
    public function getAllBy(array $criteria, ?array $orderBy = [])
    {
        $query = $this->createQueryBuilder('u');
        $query = $this->addCriteria($query, $criteria);
        $query = $this->addOrders($query, $orderBy);

        return $query->getQuery();
    }

    /**
     * @param QueryBuilder $builder
     * @param array $criteria
     * @return QueryBuilder
     */
    private function addCriteria(QueryBuilder $builder, ?array $criteria): QueryBuilder
    {
        if ($criteria === null) {
           return $builder;
        }
        
        foreach ($criteria as $criterion => $value) {
            $builder->andWhere('u.' . $criterion . '=' . ':' . $criterion)
                ->setParameter($criterion, $value);
        }

        return $builder;
    }

    /**
     * @param QueryBuilder $query
     * @param array $orders Array containing key value pairs where key = column, value = order
     * @return QueryBuilder
     */
    private function addOrders(QueryBuilder $query, ?array $orders)
    {
        if ($orders === null) {
            return $query;
        }

        foreach ($orders as $orderBy => $order) {
            $query->addorderBy('u.' . $orderBy, $order);
        }

        return $query;
    }

    /**
     * Intended to be called by PaginationHandler to paginate the query
     * @param string $pattern
     * @param null|array [field => value, ...]
     * @param null|array [field => order, ...]
     * @return Query with users that has at least one field matched given pattern and meeting criteria.
     */
    public function findByPattern(string $pattern, ?array $criteria = [], ?array $orderBy = []): Query
    {
        $query =  $this->createQueryBuilder('u')
            ->where('u.id LIKE :pattern')
            ->orWhere('u.email LIKE :pattern')
            ->orWhere('u.first_name LIKE :pattern')
            ->orWhere('u.last_name LIKE :pattern')
            ->orWhere('u.phone LIKE :pattern')
            ->orWhere('u.address LIKE :pattern')
            ->orWhere('u.city LIKE :pattern')
            ->setParameter('pattern', '%' . $pattern . '%');

        $query = $this->addCriteria($query, $criteria);
        $query = $this->addOrders($query, $orderBy);

        return $query->getQuery();
    }

    /**
     * @param null|array [field => value, ...]
     * @return int Total count of users registered and meeting criteria.
     */
    public function getCount(array $criteria = []): int
    {
        $query = $this->createQueryBuilder('u')
            ->select('count(u.id)');
        $query = $this->addCriteria($query, $criteria);

        return $query->getQuery()
            ->getSingleScalarResult();
    }
}