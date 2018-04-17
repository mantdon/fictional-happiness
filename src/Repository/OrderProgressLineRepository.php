<?php

namespace App\Repository;

use App\Entity\OrderProgressLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OrderProgressLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderProgressLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderProgressLine[]    findAll()
 * @method OrderProgressLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderProgressLineRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderProgressLine::class);
    }
}
