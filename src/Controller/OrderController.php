<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Form\VehicleType;
use App\Services\AvailableTimesFetcher;
use App\Services\MessageManager;
use App\Services\OrderCreator;
use App\Services\UnavailableDaysFinder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/order")
 */
class OrderController extends Controller
{
    /**
     * @Route("/", name="order")
     */
    public function home(Request $request)
    {
        if ($this->getUser()->getVehicles()->count() === 0) {
            $vehicle = new Vehicle();
            $form = $this->createForm(VehicleType::class, $vehicle);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $vehicle->setUser($this->getUser());
                $entityManager->persist($vehicle);
                $entityManager->flush();
            } else
                return $this->render('Order/order_vehicle_add', array(
                    'form' => $form->createView()
                ));
        }

        return $this->render('Order/base.html.twig');
    }

    /**
     * @Route("/submit")
     */
    public function submit(Request $request,
                           OrderCreator $orderCreator,
                           MessageManager $messageManager)
    {
        $content = json_decode($request->getContent(), true);

        $order = $orderCreator->createOrder($content['vehicle'], $content['services'], $content['date']);

        $messageTitle = 'Užsakymas pateiktas';
        $messageContent = $this->renderView('Email/order_placed.html.twig', array('order' => $order));
        $recipient = $this->getUser();

        $message = $messageManager->fetchOrCreateMessage($messageTitle, $messageContent);
        $messageManager->sendMessageToEmail($message, $recipient);
        $messageManager->sendMessageToProfile($message, $recipient);

        return new JsonResponse($request->request->get('services'));
    }

    /**
     * @Route("/fetch_times")
     */
    public function fetchAvailableTimes(Request $request, AvailableTimesFetcher $fetcher)
    {
        $content = json_decode($request->getContent(), true);

        $times = $fetcher->fetchDay(new \DateTime($content['date']));

        return new JsonResponse($times);
    }

    /**
     * @Route("/fetch_unavailable_days")
     */
    public function fetchUnavailableDays(Request $request, UnavailableDaysFinder $daysFinder)
    {
        $unavailableDays = $daysFinder->findDays();

        return new JsonResponse($unavailableDays);
    }
}