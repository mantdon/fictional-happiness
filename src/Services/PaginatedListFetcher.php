<?php
/**
 * Created by PhpStorm.
 * User: martynas
 * Date: 18.3.23
 * Time: 22.23
 */

namespace App\Services;


use Doctrine\ORM\EntityManagerInterface;

class PaginatedListFetcher
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getPaginatedCompletedOrders($page = 1, $limit = 5)
    {
	    $repository = $this->em->getRepository('App:Order');
	    $results = $repository->getCompleted($page, $limit);

	    $pageCount = ceil($results->count() / $limit);

	    return ['items' => $results,
		    'pageCount' => $pageCount,
		    'currentPage' => $page,
		    'totalCount' => $results->count()];
    }

    public function getOrdersForUser($page = 1, $limit = 5)
    {
	    $repository = $this->em->getRepository('App:Order');
	    $results = $repository->getForUser($page, $limit);

	    $pageCount = ceil($results->count() / $limit);

	    return ['items' => $results,
		    'pageCount' => $pageCount,
		    'currentPage' => $page,
		    'totalCount' => $results->count()];
    }

	// Used to paginate messages in user profile. Due for cleanup.
    public function getPaginatedMessagesList($user, $className, $page = 1, $limit = 5)
    {
	    $repository = $this->em->getRepository($className);
	    $results = $repository->getAll($user, $page, $limit);

	    $pageCount = ceil($results->count() / $limit);

	    return ['items' => $results,
		    'pageCount' => $pageCount,
		    'currentPage' => $page,
		    'totalCount' => $results->count()];
    }

    public function getPaginatedList($className, $page = 1, $limit = 5)
    {
        $repository = $this->em->getRepository($className);
        $results = $repository->getAll($page, $limit);

        $pageCount = ceil($results->count() / $limit);

        return ['items' => $results,
            'pageCount' => $pageCount,
            'currentPage' => $page,
            'totalCount' => $results->count()];
    }

    public function getTotalPageCount($className, $page = 1, $limit = 5){
	    $repository = $this->em->getRepository($className);
	    $results = $repository->getAll($page, $limit);

	    $pageCount = ceil($results->count() / $limit);

	    return $pageCount;
    }
}