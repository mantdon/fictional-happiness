<?php

namespace App\Helpers;

use Doctrine\ORM\Tools\Pagination\Paginator;

class Pagination
{
	public static function paginate( $dql, $page = 1, $limit = 5 )
	{
		$paginator = new Paginator( $dql );

		$paginator->getQuery()
			->setFirstResult( $limit * ( $page - 1 ) )// First element offset
			->setMaxResults( $limit );

		return $paginator;
	}
}