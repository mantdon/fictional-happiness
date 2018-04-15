<?php

namespace App\Repository;

use App\Entity\Message;
use App\Helpers\Pagination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

	// Used by PaginatedListFetcher. Due for cleanup.
	public function getAll($currentPage = 1, $limit = 5){
		$qb = $this->createQueryBuilder('message')
					->getQuery();
		$paginator = Pagination::paginate($qb, $currentPage, $limit);
		return $paginator;
	}

	public function getByTitleAndContent(string $title, string $content){
    	$qb = $this->createQueryBuilder('message');
    	$qb->select('message')
		    ->where('message.title = ?1')
		    ->andWhere('message.content = ?2')
    	    ->setParameters(array(
    	    	            1 => $title,
	                        2 => $content
	                        )
	        );

		try {
			return $qb->getQuery()->getSingleResult();
		}
		catch(NoResultException $e) {
			return new Message();
		}
	}
}
