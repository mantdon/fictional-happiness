<?php

namespace App\Controller;

use App\Services\PaginationHandler;
use App\Services\ServiceFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ServiceController
 * @package App\Controller
 * @Route ("/services")
 */
class ServiceController extends Controller
{
	/**
	 * @Route("/{page}", name="services_page", requirements={"page"="\d+"}, defaults={"page"=1}, methods="GET")
	 * @param PaginationHandler $paginationHandler
	 * @param                   $page
	 * @return Response
	 * @throws \BadMethodCallException
	 */
    public function servicePageAction(PaginationHandler $paginationHandler, $page): Response
    {
    	$paginationHandler->setQuery('App:Service', 'getAll')
					      ->setPage($page)
					      ->setItemLimit(5)
					      ->paginate();

	    return $this->render('Service/index.html.twig',
	                         ['services' => $paginationHandler->getResult(),
	                          'pageCount' => $paginationHandler->getPageCount(),
	                          'currentPage' => $paginationHandler->getCurrentPage(),
	                          'pageParameterName' => 'page',
	                          'route' => 'services_page']);
    }

	/**
	 * Testing version no longer.
	 * @Route("/search", name = "order_search_service")
	 * @param ServiceFetcher $serviceFetcher
	 * @param Request        $request
	 * @return JsonResponse
	 * @throws \LogicException
	 */
	public function searchServices(ServiceFetcher $serviceFetcher, Request $request): JsonResponse
	{
		$content = json_decode($request->getContent(), true);

		$services = $serviceFetcher->findByPattern($content['pattern']);
		return new JsonResponse($services);
	}
}
