<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Form\VehicleType;
use App\Services\ArrayNormalizer;
use App\Services\AvailableTimesFetcher;
use App\Services\MessageManager;
use App\Services\OrderCreator;
use App\Services\UnavailableDaysFinder;
use App\Services\UserManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class OrderController
 * @package App\Controller
 * @Route("/order")
 */
class OrderController extends Controller
{
	/**
	 * @Route("", name = "order")
	 * @param Request     $request
	 * @param UserManager $userManager
	 * @return Response
	 * @throws \LogicException
	 */
    public function home(Request $request, UserManager $userManager): Response
    {
        if(!$userManager->hasUserFilledPersonalInformation($this->getUser()))
        {
            $this->addFlash('notice', 'Prieš atliekant užsakymą privalote užpildyti savo informaciją');
            return $this->redirectToRoute('user_settings', ['redirect' => true]);
        }

        if ($this->getUser()->getVehicles()->count() === 0)
        {
            $vehicle = new Vehicle();
            $form = $this->createForm(VehicleType::class, $vehicle);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $entityManager = $this->getDoctrine()->getManager();
                $vehicle->setUser($this->getUser());
                $entityManager->persist($vehicle);
                $entityManager->flush();
            }
            else
            {
	            return $this->render('Order/order_vehicle_add.html.twig', ['form' => $form->createView()]);
            }
        }

        return $this->render('Order/base.html.twig');
    }

	/**
	 * @Route("/submit", name = "submit_order")
	 * @param Request        $request
	 * @param OrderCreator   $orderCreator
	 * @param MessageManager $messageManager
	 * @return Response
	 * @throws \LogicException
	 */
    public function submit(Request $request, OrderCreator $orderCreator, MessageManager $messageManager): Response
    {
        $content = json_decode($request->getContent(), true);
		$user = $this->getUser();

        $order = $orderCreator->createOrder($content['vehicle'], $content['services'], $content['date'], $user);

        $messageTitle = 'Užsakymas pateiktas';
        $messageContent = $this->renderView('Email/order_placed.html.twig', ['order' => $order]);
        $recipient = $this->getUser();

        $message = $messageManager->fetchOrCreateMessage($messageTitle, $messageContent);
        $messageManager->sendMessageToEmail($message, $recipient);
        $messageManager->sendMessageToProfile($message, $recipient);

        return new JsonResponse($request->request->get('services'));
    }

	/**
	 * @Route("/fetch_times", name = "order_fetch_times")
	 * @param Request               $request
	 * @param AvailableTimesFetcher $fetcher
	 * @return Response
	 * @throws \LogicException
	 */
    public function fetchAvailableTimes(Request $request, AvailableTimesFetcher $fetcher): Response
    {
        $content = json_decode($request->getContent(), true);
        $times = $fetcher->fetchDay($content['date']);
        return new JsonResponse($times);
    }

	/**
	 * @Route("/fetch_unavailable_days", name = "order_fetch_unavailable_days")
	 * @param UnavailableDaysFinder $daysFinder
	 * @return Response
	 */
    public function fetchUnavailableDays(UnavailableDaysFinder $daysFinder): Response
    {
        $unavailableDays = $daysFinder->findDays();
        return new JsonResponse($unavailableDays);
    }

	/**
	 * @Route("/user/vehicles/get")
	 * @param ArrayNormalizer $normalizer
	 * @return JsonResponse
	 * @throws \LogicException
	 */
	public function vehiclesShow(ArrayNormalizer $normalizer): JsonResponse
	{
		$vehicles = $this->getUser()->getVehicles();
		return new JsonResponse($normalizer->normalize($vehicles));
	}
}