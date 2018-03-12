<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/user")
 */
class UserController extends Controller
{

    /**
     * @Route("/", name="user_profile")
     */
    public function showProfile()
    {

        return $this->render(
        'profile.html.twig'
    );
    }
}