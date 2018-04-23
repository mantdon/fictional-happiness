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
        return $this->render('home.html.twig');
    }
    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        $currentDay = date('l');
        $weekDays = array('Monday', 'Thursday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        return $this->render('about.html.twig', array('currentDay' => $currentDay,
            'weekDays' => $weekDays
        ));
    }
}