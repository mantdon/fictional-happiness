<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
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

    /**
     * @Route("/api")
     */
    public function api()
    {
        $array = array(
            ['plateNumber' => 'asfa'],
            ['plateNumber' => 'ahedfb'],
        );
        return new JsonResponse($array);
    }
}