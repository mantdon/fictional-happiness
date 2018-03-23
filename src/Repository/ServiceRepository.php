<?php

namespace App\Repository;

use App\Entity\Service;
use App\Helpers\Pagination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function getAll($currentPage = 1, $limit = 5){
    	$qb = $this->createQueryBuilder('s')->getQuery();
    	$paginator = Pagination::paginate($qb, $currentPage, $limit);
    	return $paginator;
    }
}
