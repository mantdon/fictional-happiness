<?php

namespace App\Repository;

use App\Entity\Message;
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

	/**
	 * Returns a message with specified $title and $content from the database.
	 * If such message does not exist, initializes a new message.
	 * @param string $title
	 * @param string $content
	 * @return Message
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getByTitleAndContent(string $title, string $content): ?Message
	{
    	$qb = $this->createQueryBuilder('message');
    	$qb->select('message')
		    ->where('message.title = ?1')
		    ->andWhere('message.content = ?2')
    	    ->setParameters([1 => $title,
							 2 => $content]
	        );

		try {
			return $qb->getQuery()->getSingleResult();
		}
		catch(NoResultException $e) {
			return new Message();
		}
	}
}
