<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProgressLine;
use App\Entity\Vehicle;
use App\Form\VehicleType;
use App\Helpers\EnumOrderStatusType;
use App\Helpers\PreviousPageExtractor;
use App\Services\AvailableTimesFetcher;
use App\Services\MessageManager;
use App\Services\OrderCreator;
use App\Services\UnavailableDaysFinder;
use App\Services\UserManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrderController extends Controller
{
    /**
     * @Route("/order", name="order")
     */
    public function home(Request $request,
                        UserManager $userManager)
    {
        if(!$userManager->hasUserFilledPersonalInformation($this->getUser()))
        {
            $this->addFlash('notice', 'Prieš atliekant užsakymą privalote užpildyti savo informaciją');
            return $this->redirectToRoute('user_settings', array('redirect' => 1));
        }

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
    public function submit(Request $request,
                           OrderCreator $orderCreator,
                           MessageManager $messageManager)
    {
        $content = json_decode($request->getContent(), true);
		$user = $this->getUser();

        $order = $orderCreator->createOrder($content['vehicle'], $content['services'], $content['date'], $user);

        $messageTitle = 'Užsakymas pateiktas';
        $messageContent = $this->renderView('Email/order_placed.html.twig', array('order' => $order));
        $recipient = $this->getUser();

        $message = $messageManager->fetchOrCreateMessage($messageTitle, $messageContent);
        $messageManager->sendMessageToEmail($message, $recipient);
        $messageManager->sendMessageToProfile($message, $recipient);

        return new JsonResponse($request->request->get('services'));
    }

    /**
     * @Route("order/fetch_times")
     */
    public function fetchAvailableTimes(Request $request, AvailableTimesFetcher $fetcher)
    {
        $content = json_decode($request->getContent(), true);

        $times = $fetcher->fetchDay($content['date']);

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

	/**
	 * @Route("user/orders/cancel/{id}", name="user_order_cancel", requirements={"id"="\d+"})
	 */
	public function userOrderCancelAction(Order $order, OrderCreator $oc, MessageManager $mm)
	{
		if($this->getUser() === $order->getUser()){
			$satusChangeMessage = $oc->cancelOrder($order);
			$this->addFlash('notice', $satusChangeMessage);

			$messageTitle = "Užsakymas atšauktas";
			$messageBody = $this->renderView('Email/order_cancelled.html.twig',
			                                 array(
				                                 'order' => $order
			                                 ));
			$message = $mm->fetchOrCreateMessage($messageTitle, $messageBody);
			$mm->sendMessageToProfile($message, $order->getUser());
		}
		return $this->redirectToRoute('user_orders');
	}

	/**
	 * @Route("admin/orders/terminate/{id}", name="admin_order_terminate", requirements={"id"="\d+"})
	 */
	public function adminOrderTerminateAction(Order $order, OrderCreator $oc, MessageManager $mm)
	{
		$satusChangeMessage = $oc->terminateOrder($order);
		$this->addFlash('notice', $satusChangeMessage);

		$messageTitle = "Užsakymas nutrauktas";
		$messageBody = $this->renderView('Email/order_terminated.html.twig',
		                                 array(
			                                 'order' => $order
		                                 ));
		$message = $mm->fetchOrCreateMessage($messageTitle, $messageBody);
		$mm->sendMessageToProfile($message, $order->getUser());

		return $this->redirectToRoute('admin_ongoing_orders');
	}

	/**
	 * @Route("admin/orders/approve/{id}", name="admin_order_approve", requirements={"id"="\d+"})
	 */
	public function adminOrderApproveAction(Order $order, OrderCreator $oc, MessageManager $mm)
	{
		$satusChangeMessage = $oc->approveOrder($order);
		$this->addFlash('notice', $satusChangeMessage);

		$messageTitle = "Užsakymo vykdymas pradėtas";
		$messageBody = $this->renderView('Email/order_approved.html.twig',
		                                 array(
			                                 'order' => $order
		                                 ));
		$message = $mm->fetchOrCreateMessage($messageTitle, $messageBody);
		$mm->sendMessageToProfile($message, $order->getUser());

		return $this->redirectToRoute('admin_ongoing_orders');
	}

	private function savePreviousPaginationPage(Request $request){
		if(!$this->get('session')->has('previous_page')) {
			$previousPageURL = $request->headers->get( 'referer' );
			$previousPage = PreviousPageExtractor::getPreviousPage( $previousPageURL, 'page' );

			$this->get( 'session' )->set( 'previous_page', $previousPage );
		}
	}
}