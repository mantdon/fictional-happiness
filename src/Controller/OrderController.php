<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProgressLine;
use App\Entity\Vehicle;
use App\Form\VehicleType;
use App\Helpers\PreviousPageExtractor;
use App\Services\AvailableTimesFetcher;
use App\Services\MessageManager;
use App\Services\OrderCreator;
use App\Services\UnavailableDaysFinder;
use DoctrineExtensions\Query\Mysql\Date;
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
     * @Route("order/submit")
     */
    public function submit(Request $request, OrderCreator $orderCreator)
    {
        $content = json_decode($request->getContent(), true);
		$user = $this->getUser();
        $orderCreator->createOrder($content['vehicle'], $content['services'], $content['date'], $user);

        return new JsonResponse($request->request->get('services'));
    }

    /**
     * @Route("order/fetch_times")
     */
    public function fetchAvailableTimes(Request $request, AvailableTimesFetcher $fetcher)
    {
        $content = json_decode($request->getContent(), true);

        $times = $fetcher->fetchDay(new \DateTime($content['date']));

        return new JsonResponse($times);
    }

    /**
     * @Route("order/fetch_unavailable_days")
     */
    public function fetchUnavailableDays(Request $request, UnavailableDaysFinder $daysFinder)
    {
        $unavailableDays = $daysFinder->findDays();

        return new JsonResponse($unavailableDays);
    }

	/**
	 * @Route("admin/ongoingorders/{id}", name="admin_ongoing_order_show", requirements={"id"="\d+"})
	 */
	public function ongoingOrdersShowAction(Request $request, Order $order)
	{
		$this->savePreviousPaginationPage($request);
		return $this->render('Admin/OngoingOrders/admin_ongoing_order_show.html.twig',
		                     array(
			                     'order' => $order
		                     ));
	}

	/**
	 * @Route("admin/ongoingorder/completeservice/{id}", name="admin_ongoing_order_complete_service", requirements={"id": "\d+"})
	 */
	public function orderServiceCompleteAction(Request $request, OrderProgressLine $orderProgressLine, OrderCreator $oc)
	{
		$oc->completeLine($orderProgressLine);

		return $this->redirectToRoute('admin_ongoing_order_show',
			array(
				'id' => $orderProgressLine->getProgress()->getOrder()->getId()
			));
	}

	/**
	 * @Route("admin/ongoingorder/undoservice/{id}", name="admin_ongoing_order_undo_service", requirements={"id": "\d+"})
	 */
	public function orderServiceUndoAction(Request $request, OrderProgressLine $orderProgressLine, OrderCreator $oc)
	{
		$oc->undoLine($orderProgressLine);

		return $this->redirectToRoute('admin_ongoing_order_show',
		                              array(
			                              'id' => $orderProgressLine->getProgress()->getOrder()->getId()
		                              ));
	}

	/**
	 * @Route("admin/ongoingorder/finalize/{id}", name="admin_ongoing_order_finalize", requirements={"id": "\d+"})
	 */
	public function finalizeOrderAction(Request $request, Order $order, MessageManager $mm, OrderCreator $oc)
	{
		$oc->finalizeOrder($order);

		$messageTitle = "Užsakymas įvykdytas";
		$messageBody = $this->renderView('Email/order_complete.html.twig',
										array(
											'order' => $order
										));
		$message = $mm->fetchOrCreateMessage($messageTitle, $messageBody);
		$mm->sendMessageToProfile($message, $order->getUser());
		$mm->sendMessageToEmail($message, $order->getUser());

		return $this->redirectToRoute('admin_ongoing_orders');
	}

	/**
	 * @Route("admin/completedorders/{id}", name="admin_completed_order_show", requirements={"id"="\d+"})
	 */
	public function completedOrdersShowAction(Request $request, Order $order)
	{
		$this->savePreviousPaginationPage($request);
		return $this->render('Admin/CompletedOrders/admin_completed_order_show.html.twig',
		                     array(
			                     'order' => $order
		                     ));
	}

	/**
	 * @Route("user/orders/{id}", name="user_order_show", requirements={"id"="\d+"})
	 */
	public function userOrderShowAction(Request $request, Order $order)
	{
		$this->savePreviousPaginationPage($request);
		return $this->render('Profile/Orders/profile_orders_show.html.twig',
		                     array(
		                     	'user' => $this->getUser(),
			                     'order' => $order
		                     ));
	}

	private function savePreviousPaginationPage(Request $request){
		if(!$this->get('session')->has('previous_page')) {
			$previousPageURL = $request->headers->get( 'referer' );
			$previousPage = PreviousPageExtractor::getPreviousPage( $previousPageURL, 'page' );

			$this->get( 'session' )->set( 'previous_page', $previousPage );
		}
	}
}