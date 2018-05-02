<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use App\Helpers\EnumOrderStatusType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
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

	/**
	 * Intended to be called by PaginationHandler to paginate the query
	 * @return Query a query for all orders flagged as placed or ongoing
	 * sorted by status and visit date.
	 */
	public function getValidOrdersForAdmin(): Query
	{
		return $this->createQueryBuilder('o')
					->leftJoin('o.progress', 'p')
					->leftJoin('o.user', 'u')
					->where('o.status = ?1')
					->orWhere('o.status = ?2')
					->andWhere('u.isEnabled = 1')
					->orderBy('o.status', 'ASC')
					->addOrderBy('o.visitDate', 'ASC')
					->setParameter(1, EnumOrderStatusType::Placed)
					->setParameter(2, EnumOrderStatusType::Ongoing)
					->getQuery();
	}

	/**
	 * Intended to be called by PaginationHandler to paginate the query
	 * @return Query a query for all orders flagged as complete sorted
	 * by completion date in descending order.
	 */
	public function getCompletedOrdersForAdmin(): Query
	{
		return $this->createQueryBuilder('o')
					->leftJoin('o.progress', 'progress')
					->where('o.status = ?1')
					->orderBy('progress.completionDate', 'DESC')
					->setParameter(1, EnumOrderStatusType::Complete)
					->getQuery();
	}

	/**
	 * Intended to be called by PaginationHandler to paginate the query
	 * @param User $user
	 * @return Query a query for all orders placed by the $user sorted by status,
	 * visit and completion dates.
	 */
	public function getUserOrders(User $user): Query
	{
		return $this->createQueryBuilder('o')
					->where('o.user = ?1')
					->leftJoin('o.progress', 'p')
					->orderBy('o.status', 'ASC')
					->addOrderBy('p.completionDate', 'DESC')
					->addOrderBy('o.visitDate', 'ASC')
					->setParameter(1, $user)
					->getQuery();
	}

	/**
	 * @param $date \DateTime
	 * @return mixed
	 */
    public function findAllOnDate(\DateTime $date)
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

	/**
	 * @param $date \DateTime
	 * @return mixed
	 */
    public function findAllOnMonth(\DateTime $date)
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
