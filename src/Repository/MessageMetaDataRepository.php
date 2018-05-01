<?php

namespace App\Repository;

use App\Entity\MessageMetaData;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MessageMetaData|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageMetaData|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageMetaData[]    findAll()
 * @method MessageMetaData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageMetaDataRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MessageMetaData::class);
    }

	/**
	 * Intended to be called by PaginationHandler to paginate the query
	 * @param User $user
	 * @return Query a query for all messages sent to the $user, sorted by
	 * sending date in descending order.
	 */
	public function getUserMessages(User $user): Query
	{
    	return $this->createQueryBuilder('messageMetaData')
				    ->where('messageMetaData.recipient = ?1')
				    ->andWhere('messageMetaData.isDeletedByUser = 0')
				    ->orderBy('messageMetaData.dateSent', 'DESC')
				    ->setParameter(1, $user)
		            ->getQuery();
	}
}
