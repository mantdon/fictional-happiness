<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrderController extends Controller
{
    /**
     * @Route("/order", name="order")
     */
    public function home()
    {
        //For testing purposes.
        $array = array(
            ['plateNumber' => 'asfa'],
            ['plateNumber' => 'ahedfb'],
        );
        return $this->render('Order/base.html.twig',
            array('vehicles' => $array));
    }
}