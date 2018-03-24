<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
	/**
	 * @Route("/user", name="user_home")
	 */
    public function showProfile()
    {
	    return $this->redirectToRoute('user_vehicles');
    }
}