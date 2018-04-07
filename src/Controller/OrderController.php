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
        return $this->render('Order/base.html.twig');
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