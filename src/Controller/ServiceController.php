<?php

namespace App\Controller;

use App\Services\PaginatedListFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ServiceController extends Controller
{
	private $pageParameterName = "page";
	private $limitParameterName = "limit";

	/**
	 * @Route("/services", name="services")
	 */
    public function listAction(Request $request, PaginatedListFetcher $listFetcher){
    	$page = $request->query->getInt($this->pageParameterName, 1);

        $list = $listFetcher->getPaginatedList('App:Service', $page);

	    // Load first page on invalid page entry
    	if($page > $list['pageCount'] || $page < 1)
    		return $this->redirectToRoute('services', array($this->pageParameterName => 1));

        return $this->render(
    		'Service/index.html.twig',
		    array('services' => $list['items'],
			      'pageCount' => $list['pageCount'],
		          'currentPage' => $page,
			      'pageParameterName' => $this->pageParameterName,
		          'route' => "services")
	    );
    }
}
