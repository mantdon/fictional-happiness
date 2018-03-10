<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function home()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/user", name="user_profile")
     */
    public function userSomething()
    {
        return new Response('<html><body><h3>USER AREA</h3></body></html>');
    }
}