<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Form\VehicleType;
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
    public function home(Request $request)
    {
        if($this->getUser()->getVehicles()->count() === 0) {
            $vehicle = new Vehicle();
            $form = $this->createForm( VehicleType::class, $vehicle);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $vehicle->setUser($this->getUser());
                $entityManager->persist($vehicle);
                $entityManager->flush();
            }
            else
                return $this->render('Order/order_vehicle_add', array(
                    'form' => $form->createView()
                ));
        }

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