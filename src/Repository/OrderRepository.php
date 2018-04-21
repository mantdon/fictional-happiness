<?php

namespace App\Repository;

use App\Entity\Order;
use App\Helpers\Pagination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Order::class);
    }

	// Used by PaginatedListFetcher. Due for cleanup.
	public function getAll($currentPage = 1, $limit = 5){
		$qb = $this->createQueryBuilder('o')
					->leftJoin('o.progress', 'progress')
					->where('progress.isDone = 0')
					->orderBy('o.visitDate', 'ASC')
					->getQuery();
		$paginator = Pagination::paginate($qb, $currentPage, $limit);
		return $paginator;
	}

	public function getCompleted($currentPage = 1, $limit = 5)
	{
		$qb = $this->createQueryBuilder('o')
					->leftJoin('o.progress', 'progress')
					->where('progress.isDone = 1')
					->orderBy('progress.completionDate', 'DESC')
					->getQuery();
		$paginator = Pagination::paginate($qb, $currentPage, $limit);
		return $paginator;
	}

	public function getForUser(\App\Entity\User $user, $currentPage = 1, $limit = 5)
	{
		$qb = $this->createQueryBuilder('o')
			->where('o.user = ?1')
			->leftJoin('o.progress', 'progress')
			->orderBy('progress.isDone', 'ASC')
			->addOrderBy('o.visitDate', 'DESC')
			->addOrderBy('progress.completionDate', 'ASC')
			->setParameter(1, $user)
			->getQuery();
		$paginator = Pagination::paginate($qb, $currentPage, $limit);
		return $paginator;
	}

    /**
     * @param $date \DateTime
     */
    public function findAllOnDate($date)
    {
        return $this->createQueryBuilder('a')
            ->where('YEAR(a.visitDate) = YEAR(:date) AND
             MONTH(a.visitDate) = MONTH(:date) AND 
             DAY(a.visitDate) = DAY(:date)')
            ->setParameter('date', $date)
            ->orderBy('a.visitDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllOnMonth($date)
    {
        return $this->createQueryBuilder('a')
            ->where('YEAR(a.visitDate) = YEAR(:date) AND
             MONTH(a.visitDate) = MONTH(:date)')
            ->orderBy('a.visitDate', 'ASC')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}
