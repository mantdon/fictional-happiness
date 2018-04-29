<?php

namespace App\Controller;

use App\Entity\Order;
use App\Services\PaginatedListFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/**
 * @Route("/employee")
 */
class EmployeeController extends Controller
{
    private $pageParameterName = "page";

    /**
     * @Route("/", name="employee_home")
     */
    public function home()
    {
        return $this->render( 'Employee/Home/employee_home.html.twig' );
    }
    /**
     * @Route("/users", name="employee_users")
     */
    public function usersAction( Request $request )
    {
        $page = $this->loadPageValue( $request, 1 );
        return $this->redirectToRoute( 'employee_users_page', [ $this->pageParameterName => $page ] );
    }

    /**
     * @Route ("/users/{page}", name="employee_users_page", requirements={"page"="\d+"})
     */
    public function usersPageAction( PaginatedListFetcher $listFetcher, $page )
    {
        $list = $listFetcher->getPaginatedList( 'App:User', $page );
        // Load first page on invalid page entry.
        // 'totalCount' check is needed to avoid redirection loops on empty list.
        if( $list['totalCount'] !== 0 && $page > $list['pageCount'] || $page < 1 )
            return $this->redirectToRoute( 'employee_users_page', array( $this->pageParameterName => 1 ) );
        return $this->render( 'Employee/Users/list.html.twig',
            array( 'users' => $list['items'],
                'pageCount' => $list['pageCount'],
                'userCount' => $list['totalCount'],
                'currentPage' => $page,
                'pageParameterName' => $this->pageParameterName,
                'route' => 'employee_users_page' ) );
    }

    /**
     * @Route("/ongoingorders", name="employee_ongoing_orders")
     */
    public function ongoingOrdersAction(Request $request)
    {
        $page = $this->loadPageValue( $request );
        return $this->redirectToRoute( 'employee_ongoing_orders_page', [ $this->pageParameterName => $page ] );
    }

    /**
     * @Route("/ongoingorders/page/{page}", name="employee_ongoing_orders_page", requirements={"page"="\d+"})
     */
    public function ongoingOrdersPageAction( PaginatedListFetcher $listFetcher, $page )
    {
        $list = $listFetcher->getPaginatedList( 'App:Order', $page , 4);
        // Load first page on invalid page entry.
        // 'totalCount' check is needed to avoid redirection loops on empty list.
        if( $list['totalCount'] !== 0 && $page > $list['pageCount'] || $page < 1 )
            return $this->redirectToRoute( 'employee_ongoing_orders_page', array( $this->pageParameterName => 1 ) );

        return $this->render( 'Employee/OngoingOrders/employee_ongoing_orders.html.twig',
            array( 'orders' => $list['items'],
                'pageCount' => $list['pageCount'],
                'currentPage' => $page,
                'pageParameterName' => $this->pageParameterName,
                'route' => 'employee_ongoing_orders_page' ) );
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
