<?php

namespace App\Controller;

use App\Services\OrderCreator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
            ['id' => 0, 'plateNumber' => 'asfa'],
            ['id' => 1, 'plateNumber' => 'ahedfb'],
        );
        return new JsonResponse($array);
    }

    /**
     * @Route("order/submit")
     */
    public function submit(Request $request, OrderCreator $orderCreator)
    {
        $content = json_decode($request->getContent(), true);

        $orderCreator->createOrder($content['vehicle'], $content['services']);

        return new JsonResponse($request->request->get('services'));
    }
}