<?php

namespace App\Controller;

use App\Form\SearchType;
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
	 * @Route("/{page}", name="services_page", requirements={"page"="\d+"}, defaults={"page"=1})
     * @param Request           $request
	 * @param PaginationHandler $paginationHandler
	 * @param                   $page
	 * @return Response
	 * @throws \BadMethodCallException
	 */
    public function servicePageAction(Request $request, PaginationHandler $paginationHandler, $page): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            return $this->redirectToRoute('order_search_service', array(
                'key' => $formData['key']
            ));

        }
    	$paginationHandler->setQuery('App:Service', 'getAll')
					      ->setPage($page)
					      ->setItemLimit(5)
					      ->paginate();

	    return $this->render('Service/index.html.twig', array(
            'services' => $paginationHandler->getResult(),
            'pageCount' => $paginationHandler->getPageCount(),
            'currentPage' => $paginationHandler->getCurrentPage(),
            'pageParameterName' => 'page',
            'route' => 'services_page',
            'form' => $form->createView()
        ));
    }

	/**
	 * @Route("/search/{key}/{page}", name = "order_search_service", requirements={"page"="\d+"}, defaults={"page"=1})
	 * @param PaginationHandler $paginationHandler
     * @param                   $page
     * @param                   $key
	 * @return Response
	 * @throws \LogicException
	 */
	public function searchServices(PaginationHandler $paginationHandler, $page, $key): Response
	{
        $paginationHandler->setQuery('App:Service', 'findByPattern', $key)
            ->setPage($page)
            ->setItemLimit(5)
            ->paginate();

        return $this->render('Service/search.html.twig', array(
            'services' => $paginationHandler->getResult(),
            'pageCount' => $paginationHandler->getPageCount(),
            'currentPage' => $paginationHandler->getCurrentPage(),
            'pageParameterName' => 'page',
            'route' => 'order_search_service',
            'key' => $key
        ));
	}

	/**
     * @Route("/get", name = "order_get_services")
     * @param ServiceFetcher $serviceFetcher
     * @param Request        $request
     * @return JsonResponse
     */
	public function getAllServices(ServiceFetcher $serviceFetcher, Request $request)
    {
        $services = $serviceFetcher->findAll();
        return new JsonResponse($services);
    }
}
