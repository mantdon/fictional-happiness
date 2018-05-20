<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserList
{
    private $paginationHandler;
    private $entityManager;

    public function __construct(PaginationHandler $paginationHandler, EntityManagerInterface
    $entityManager)
    {
        $this->paginationHandler = $paginationHandler;
        $this->entityManager = $entityManager;
    }

    public function getPaginatedList(string $method, int $page, int $itemsLimit, ...$args): PaginationHandler
    {
         $this->paginationHandler->setQuery('App:User', $method, ...$args)
            ->setPage($page)
            ->setItemLimit($itemsLimit)
            ->addLastUsedPageUseCase('/users/ban')
            ->addLastUsedPageUseCase('/users/unban')
            ->paginate();

         return $this->paginationHandler;
    }

    public function getRegisteredUsersCount(): int
    {
        return $this->entityManager->getRepository(User::class)->getCount();
    }
}