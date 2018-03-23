<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
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
    public function showUserList()
    {
        return $this->render('Admin/UserList/list.html.twig');
    }
}