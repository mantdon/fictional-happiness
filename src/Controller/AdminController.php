<?php

namespace App\Controller;

use App\Entity\Order;
use App\Services\PaginatedListFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
	private $pageParameterName = "page";

	/**
	 * @Route("/", name="admin_home")
	 */
	public function home()
	{
		return $this->render( 'Admin/Home/admin_home.html.twig' );
	}

	/**
	 * @Route("/users", name="admin_users")
	 */
	public function usersAction( Request $request )
	{
		$page = $this->loadPageValue( $request, 1 );
		return $this->redirectToRoute( 'admin_users_page', [ $this->pageParameterName => $page ] );
	}

	/**
	 * @Route ("/users/{page}", name="admin_users_page", requirements={"page"="\d+"})
	 */
	public function usersPageAction( PaginatedListFetcher $listFetcher, $page )
	{
		$list = $listFetcher->getPaginatedList( 'App:User', $page );
		// Load first page on invalid page entry.
		// 'totalCount' check is needed to avoid redirection loops on empty list.
		if( $list['totalCount'] !== 0 && $page > $list['pageCount'] || $page < 1 )
			return $this->redirectToRoute( 'admin_users_page', array( $this->pageParameterName => 1 ) );
		return $this->render( 'Admin/Users/list.html.twig',
		                      array( 'users' => $list['items'],
			                      'pageCount' => $list['pageCount'],
			                      'userCount' => $list['totalCount'],
			                      'currentPage' => $page,
			                      'pageParameterName' => $this->pageParameterName,
			                      'route' => 'admin_users_page' ) );
	}

	/**
	 * @Route ("/services", name="admin_services")
	 */
	public function servicesAction( Request $request, PaginatedListFetcher $listFetcher )
	{
		$page = $this->loadPageValue( $request, 1 );
		return $this->redirectToRoute( 'admin_services_page', [ $this->pageParameterName => $page ] );
	}

	/**
	 * @Route ("/services/{page}", name="admin_services_page", requirements={"page"="\d+"})
	 */
	public function servicePageAction( PaginatedListFetcher $listFetcher, $page )
	{
		$list = $listFetcher->getPaginatedList( 'App:Service', $page );
		// Load first page on invalid page entry.
		// 'totalCount' check is needed to avoid redirection loops on empty list.
		if( $list['totalCount'] !== 0 && $page > $list['pageCount'] || $page < 1 )
			return $this->redirectToRoute( 'admin_services_page', array( $this->pageParameterName => 1 ) );

		$this->saveFinalPaginationPage( $list['pageCount'] );
		return $this->render( 'Admin/Services/admin_services.html.twig',
		                      array( 'services' => $list['items'],
			                      'pageCount' => $list['pageCount'],
			                      'currentPage' => $page,
			                      'pageParameterName' => $this->pageParameterName,
			                      'route' => 'admin_services_page' ) );
	}

	/**
	 * @Route("/ongoingorders", name="admin_ongoing_orders")
	 */
	public function ongoingOrdersAction(Request $request)
	{
		$page = $this->loadPageValue( $request );
		return $this->redirectToRoute( 'admin_ongoing_orders_page', [ $this->pageParameterName => $page ] );
	}

	/**
	 * @Route("/ongoingorders/page/{page}", name="admin_ongoing_orders_page", requirements={"page"="\d+"})
	 */
	public function ongoingOrdersPageAction( PaginatedListFetcher $listFetcher, $page )
	{
		$list = $listFetcher->getPaginatedList( 'App:Order', $page , 4);
		// Load first page on invalid page entry.
		// 'totalCount' check is needed to avoid redirection loops on empty list.
		if( $list['totalCount'] !== 0 && $page > $list['pageCount'] || $page < 1 )
			return $this->redirectToRoute( 'admin_ongoing_orders_page', array( $this->pageParameterName => 1 ) );

		return $this->render( 'Admin/OngoingOrders/admin_ongoing_orders.html.twig',
		                      array( 'orders' => $list['items'],
			                      'pageCount' => $list['pageCount'],
			                      'currentPage' => $page,
			                      'pageParameterName' => $this->pageParameterName,
			                      'route' => 'admin_ongoing_orders_page' ) );
	}

	/**
	 * @Route("/admin/completedorders", name="admin_completed_orders")
	 */
	public function completedOrdersAction(Request $request)
	{
		$page = $this->loadPageValue( $request );
		return $this->redirectToRoute( 'admin_completed_orders_page', [ $this->pageParameterName => $page ] );
	}

	/**
	 * @Route("/admin/completedorders/page/{page}", name="admin_completed_orders_page", requirements={"page": "\d+"})
	 */
	public function completedOrdersPageAction(PaginatedListFetcher $listFetcher, $page)
	{
		$list = $listFetcher->getPaginatedCompletedOrders($page);
		// Load first page on invalid page entry.
		// 'totalCount' check is needed to avoid redirection loops on empty list.
		if( $list['totalCount'] !== 0 && $page > $list['pageCount'] || $page < 1 )
			return $this->redirectToRoute( 'admin_completed_orders_page', array( $this->pageParameterName => 1 ) );

		return $this->render( 'Admin/CompletedOrders/admin_completed_orders.html.twig',
		                      array( 'orders' => $list['items'],
			                      'pageCount' => $list['pageCount'],
			                      'currentPage' => $page,
			                      'pageParameterName' => $this->pageParameterName,
			                      'route' => 'admin_completed_orders_page' ) );
	}

	private function saveFinalPaginationPage(int $page_count){
		if(!$this->get('session')->has('last_page')) {
			$this->get( 'session' )->set('last_page', $page_count );
		}
	}

    private function loadPageValue(Request $request, $defaultValue = 1){
	    $previous_page_key = 'previous_page';
    	$last_page_key = 'last_page';
    	$page = NULL;
        if($this->lastPageRequired($request))
        	$page = $this->loadAndClearPageFromSession($last_page_key);
        elseif($this->previousPageRequired($previous_page_key))
        	$page = $this->loadAndClearPageFromSession($previous_page_key);
		if($page === NULL)
			return $defaultValue;
        return $page;
    }

    private function lastPageRequired(Request $request){
		// Going to last page if coming back from creating a new service.
	    preg_match("/new/", $request->headers->get('referer'), $match);
	    if(count($match) === 1)
		    return true;
	    return false;
    }

    private function previousPageRequired(string $previous_page_key){
	    if($this->get('session')->has($previous_page_key))
	    	return true;
	    return false;
    }

	public function loadAndClearPageFromSession(string $page_key){
		$savedPage = $this->get( 'session' )->get($page_key);
		$this->get('session')->remove($page_key);

		return $savedPage;
	}
}