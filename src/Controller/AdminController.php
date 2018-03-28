<?php

namespace App\Controller;

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
     * @Route("/", name="admin.homepage")
     */
    public function home()
    {
        return $this->render('Admin/Home/admin_home.html.twig');
    }

    /**
     * @Route("/users", name="admin.user_list")
     */
    public function showUserList(Request $request, PaginatedListFetcher $listFetcher)
    {
        $page = $request->query->getInt($this->pageParameterName, 1);

        $list = $listFetcher->getPaginatedList('App:User', $page);
        return $this->render('Admin/UserList/list.html.twig',
            array('users' => $list['items'],
                'pageCount' => $list['pageCount'],
                'userCount' => $list['totalCount'],
                'currentPage' => $page,
                'pageParameterName' => $this->pageParameterName,
                'route' => 'admin.user_list'));
    }

	/**
	 * @Route ("/services", name="admin_services")
	 */
    public function servicesAction(Request $request, PaginatedListFetcher $listFetcher){
    	$page = $this->loadPageValue($request, 1);
	    return $this->redirectToRoute('admin_services_page', [$this->pageParameterName => $page]);
    }

	/**
	 * @Route ("/services/{page}", name="admin_services_page", requirements={"page"="\d+"})
	 */
	public function servicePageAction(Request $request,  PaginatedListFetcher $listFetcher, $page){
		$list = $listFetcher->getPaginatedList('App:Service', $page);
		// Load first page on invalid page entry.
		// 'totalCount' check is needed to avoid redirection loops on empty list.
		if($list['totalCount'] !== 0 && $page > $list['pageCount'] || $page < 1)
			return $this->redirectToRoute('admin_services_page', array($this->pageParameterName => 1));

		$this->saveFinalPaginationPage($list['pageCount']);
		return $this->render('Admin/Services/admin_services.html.twig',
		                     array('services' => $list['items'],
			                     'pageCount' => $list['pageCount'],
			                     'currentPage' => $page,
			                     'pageParameterName' => $this->pageParameterName,
			                     'route' => 'admin_services_page'));
	}

	private function saveFinalPaginationPage(int $page_count){
		if(!$this->get('session')->has('last_page')) {
			$this->get( 'session' )->set('last_page', $page_count );
		}
	}

    private function loadPageValue(Request $request, $defaultValue){
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

    /**
     * @Route("/users/delete/{id}", name="delete.user")
     */
    public function deleteUser($id)
    {
        $entityManager = $this->getDoctrine()->getEntityManager();
        $user = $entityManager->getRepository('App:User')->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->render('Admin/delete_user.html.twig', [
        'id' => $id
        ]);
    }
}