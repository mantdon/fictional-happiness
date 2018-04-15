<?php

namespace App\Repository;

use App\Entity\MessageMetaData;
use App\Helpers\Pagination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    // Used by PaginatedListFetcher. Due for cleanup.
	public function getAll($user, $currentPage = 1, $limit = 5){
		$qb = $this->createQueryBuilder('messageMetaData')
					->where('messageMetaData.recipient = ?1')
					->andWhere('messageMetaData.isDeletedByUser = 0')
					->orderBy('messageMetaData.dateSent', 'DESC')
					->setParameter(1, $user)
					->getQuery();
		$paginator = Pagination::paginate($qb, $currentPage, $limit);
		return $paginator;
	}

	public function getMetaDataOfUserMessages(User $user)
	{
    	return $this->createQueryBuilder('messageMetaData')
				    ->where('messageMetaData.recipient = ?1')
				    ->setParameter(1, $user)
		            ->getQuery()
		            ->getResult();
	}
}
