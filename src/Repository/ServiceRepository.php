<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Service::class);
    }

	/**
	 * Intended to be called by PaginationHandler to paginate the query.
	 * @return Query a query for all services.
	 */
    public function getAll(): Query
    {
	    return $this->createQueryBuilder('s')->getQuery();
    }

	public function findByPattern($pattern)
	{
        return $this->createQueryBuilder('a')
			->where('a.name LIKE :name')
			->setParameter('name', '%'. $pattern .'%')
			->getQuery();
	}
}
