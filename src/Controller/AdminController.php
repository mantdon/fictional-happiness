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
        return $this->render('Admin/base.html.twig');
    }

    /**
     * @Route("/users", name="admin.user_list")
     */
    public function showUserList(Request $request, PaginatedListFetcher $listFetcher)
    {
        $page = $request->query->getInt($this->pageParameterName, 1);

        $list = $listFetcher->getPaginatedList('App:User', $page);
        return $this->render('Admin/UserList/list.html.twig',
            array('list' => $list,
                'pageParameterName' => $this->pageParameterName,
                'route' => "admin.user_list"));
    }
}