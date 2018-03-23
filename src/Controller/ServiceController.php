<?php

namespace App\Controller;

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
    public function listAction(Request $request){
    	$page = $request->query->getInt($this->pageParameterName, 1);
	    // Hardcoded limit for now
    	$limit = 5;

    	$repository = $this->getDoctrine()->getRepository('App:Service');
    	$services = $repository->getAll($page, $limit);

    	$pageCount = ceil($services->count() / $limit);

    	return $this->render(
    		'Service/index.html.twig',
		    array('services' => $services,
			      'pageCount' => $pageCount,
		          'currentPage' => $page,
			      'pageParameterName' => $this->pageParameterName,
		          'route' => "services")
	    );
    }
}
