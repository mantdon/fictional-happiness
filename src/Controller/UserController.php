<?php

namespace App\Controller;

use App\Entity\ChangePassword;
use App\Entity\MessageMetaData;
use App\Entity\Order;
use App\Entity\Vehicle;
use App\Form\ChangePasswordType;
use App\Form\EditUserType;
use App\Form\VehicleType;
use App\Services\MessageManager;
use App\Services\OrderCreator;
use App\Services\PaginationHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends Controller
{
	private $pageParameterName = 'page';

	/**
	 * @Route("", name="user_home")
	 */
    public function showProfile()
    {
	    return $this->redirectToRoute('user_vehicles');
    }

	//<editor-fold desc="Vehicles">
	/**
	 * @Route("/vehicles", name="user_vehicles")
	 * @throws \LogicException
	 */
	public function vehiclesTabAction()
	{
		return $this->render('Profile/Vehicles/profile_vehicles.html.twig',
		                     [
			                     'user' => $this->getUser(),
			                     'vehicles' => $this->getUser()->getVehicles()
		                     ]
		);
	}

	/**
	 * @Route("/vehicles/add", name="add_vehicle")
	 * @param Request $request
	 * @return Response
	 * @throws \LogicException
	 */
	public function vehiclesAddAction(Request $request): Response
	{
		$vehicle = new Vehicle();
		$form = $this->createForm( VehicleType::class, $vehicle);

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$entityManager = $this->getDoctrine()->getManager();
			$vehicle->setUser($this->getUser());
			$entityManager->persist( $vehicle );
			$entityManager->flush();
			$this->addFlash('notice', 'Vehicle added[PH]');
			return $this->redirectToRoute( 'user_vehicles' );
		}

		return $this->render('Profile/Vehicles/profile_vehicles_add.html.twig',
		                     [
			                     'user' => $this->getUser(),
			                     'vehicles' => $this->getUser()->getVehicles(),
			                     'form' => $form->createView()
		                     ]
		);
	}

	/**
	 * @Route("/vehicles/edit/{id}", defaults={"id"=null}, name="edit_vehicle")
	 * @param Request      $request
	 * @param Vehicle|null $vehicle
	 * @return Response
	 * @throws \LogicException
	 */
	public function vehicleEditAction(Request $request, Vehicle $vehicle = NULL): Response
	{
		if($vehicle !== NULL && $vehicle->getUser() === $this->getUser()) {
			$form = $this->createForm( VehicleType::class, $vehicle );

			$form->handleRequest( $request );

			if( $form->isSubmitted() && $form->isValid()) {
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist( $vehicle );
				$entityManager->flush();
				$this->addFlash('notice', 'Vehicle updated[PH]');

				return $this->redirectToRoute( 'user_vehicles' );
			}

			return $this->render( 'Profile/Vehicles/profile_vehicles_edit.html.twig',
			                      [
				                      'user' => $this->getUser(),
				                      'vehicles' => $this->getUser()->getVehicles(),
				                      'form' => $form->createView()
			                      ]
			);
		}
		return $this->redirectToRoute('user_vehicles');
	}

	/**
	 * @Route("/vehicles/delete/{id}", name="show_vehicle", methods="GET")
	 * @param Vehicle $vehicle
	 * @return Response
	 * @throws \LogicException
	 */
	public function vehicleDeleteConfirmAction(Vehicle $vehicle): Response
	{
		if($vehicle !== NULL && $vehicle->getUser() === $this->getUser())
		{
			return $this->render( 'Profile/Vehicles/profile_vehicles_delete_confirm.html.twig',
			                      [
				                      'user' => $this->getUser(),
				                      'vehicles' => $this->getUser()->getVehicles(),
				                      'vehicle' => $vehicle
			                      ]
			);
		}

		return $this->redirectToRoute('user_vehicles');
	}

	/**
	 * @Route("/vehicles/delete/{id}", name="delete_vehicle", methods="DELETE")
	 * @param Request $request
	 * @param Vehicle $vehicle
	 * @return Response
	 * @throws \LogicException
	 */
	public function vehicleDeleteAction(Request $request, Vehicle $vehicle): Response
	{
		if ($vehicle->getUser() === $this->getUser() &&
			$this->isCsrfTokenValid('delete'.$vehicle->getId(), $request->request->get('_token'))
		)
		{
			$em = $this->getDoctrine()->getManager();
			$em->remove($vehicle);
			$em->flush();
			$this->addFlash('notice', 'Vehicle deleted[PH]');
		}

		return $this->redirectToRoute('user_vehicles');
	}
	//</editor-fold>

	//<editor-fold desc="Orders">
	/**
	 * @Route("/orders/{page}", name="user_orders", defaults={"page"=1}, requirements={"page"="\d+"})
	 * @param PaginationHandler $paginationHandler
	 * @param                   $page
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 * @throws \BadMethodCallException
	 */
	public function ordersPageAction(PaginationHandler $paginationHandler, $page)
	{
		$paginationHandler->setQuery('App:Order', 'getUserOrders', $this->getUser())
			->setPage($page)
			->setItemLimit(3)
			->addLastUsedPageUseCase('orders/view')
			->addLastUsedPageUseCase('orders/cancel')
			->paginate();

		return $this->render( 'Profile/Orders/profile_orders.html.twig',
		                      [
			                      'user' => $this->getUser(),
			                      'orders' => $paginationHandler->getResult(),
			                      'pageCount' => $paginationHandler->getPageCount(),
			                      'currentPage' => $paginationHandler->getCurrentPage(),
			                      'pageParameterName' => $this->pageParameterName,
			                      'route' => 'user_orders'
		                      ]
		);
	}

	/**
	 * @Route("/orders/view/{id}", name="user_order_show", requirements={"id"="\d+"})
	 * @param Order $order
	 * @return Response
	 */
	public function orderShowAction(Order $order): Response
	{
		#$this->savePreviousPaginationPage($request);
		return $this->render('Profile/Orders/profile_orders_show.html.twig',
		                     array(
			                     'user' => $this->getUser(),
			                     'order' => $order
		                     ));
	}

	/**
	 * @Route("/orders/cancel/{id}", name="user_order_cancel", requirements={"id"="\d+"})
	 * @param Order          $order
	 * @param OrderCreator   $oc
	 * @param MessageManager $mm
	 * @return Response
	 * @throws \LogicException
	 */
	public function orderCancelAction(Order $order, OrderCreator $oc, MessageManager $mm): Response
	{
		if($this->getUser() === $order->getUser()){
			$statusChangeMessage = $oc->cancelOrder($order);
			$this->addFlash('notice', $statusChangeMessage);

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
	//</editor-fold>

	//<editor-fold desc="Messages">
	/**
	 * @Route("/messages/{page}", name="user_messages", defaults={"page"=1} ,requirements={"page"="\d+"})
	 * @param PaginationHandler $paginationHandler
	 * @param                   $page
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 * @throws \LogicException
	 */
	public function messagesPageAction(PaginationHandler $paginationHandler, $page){
		$paginationHandler->setQuery('App:MessageMetaData', 'getUserMessages', $this->getUser())
			->setPage($page)
			->setItemLimit(4)
			->addLastUsedPageUseCase('messages/view')
			->paginate();

		return $this->render('Profile/Messages/profile_messages.html.twig',
		                     [
			                     'user' => $this->getUser(),
			                     'messages' => $paginationHandler->getResult(),
			                     'pageCount' => $paginationHandler->getPageCount(),
			                     'messagesCount' => $paginationHandler->getResult()->getTotalCount(),
			                     'currentPage' => $paginationHandler->getCurrentPage(),
			                     'pageParameterName' => $this->pageParameterName,
			                     'route' => 'user_messages'
		                     ]
		);
	}

	/**
	 * @Route("/messages/view/{id}", name="show_message", requirements={"id"="\d+"}, methods="GET")
	 * @param MessageMetaData $messageMetaData
	 * @param MessageManager  $mm
	 * @return Response
	 * @throws \LogicException
	 */
	public function messageViewAction(MessageMetaData $messageMetaData, MessageManager $mm): Response
	{
		if($messageMetaData !== NULL && $mm->messageMeantForUser($messageMetaData, $this->getUser()))
		{
			$mm->markMessageAsReadIfUnread($messageMetaData);
			return $this->render( 'Profile/Messages/profile_message_show.html.twig',
			                      [
									'messageData' => $messageMetaData,
									'user' => $this->getUser(),
			                      ]
			);
		}

		return $this->redirectToRoute('user_messages');
	}

	/**
	 * @Route("/messages/delete/{id}", name="delete_message", methods="DELETE")
	 * @param Request         $request
	 * @param MessageMetaData $messageMetaData
	 * @param MessageManager  $mm
	 * @return Response
	 * @throws \LogicException
	 */
	public function messageDeleteAction(Request $request, MessageMetaData $messageMetaData, MessageManager $mm): Response
	{
		if($mm->messageMeantForUser($messageMetaData, $this->getUser()) &&
			$this->isCsrfTokenValid('delete'.$messageMetaData->getId(), $request->request->get('_token'))
		)
		{
			$mm->markMessageAsDeletedIfNotDeleted($messageMetaData);
			$this->addFlash('notice', 'Message deleted[PH]');
		}

		return $this->redirectToRoute('user_messages');
	}
	//</editor-fold>

	//<editor-fold desc="Settings">
	/**
	 * @Route("/settings/{redirect}", name="user_settings")
	 * @param Request $request
	 * @param bool    $redirect Whether to redirect the user the user
	 * to order placement page after successful profile update.
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 * @throws \LogicException
	 */
	public function settingsTabAction(Request $request, $redirect = false)
	{
		$user = $this->getUser();
		$form = $this->createForm(EditUserType::class, $user);
		$form->handleRequest($request);
		$entityManager = $this->getDoctrine()->getManager();

		if( $form->isSubmitted() && $form->isValid())
		{
			$entityManager->persist($user);
			$entityManager->flush();
			$this->addFlash('notice', 'User updated[PH]');

			if($redirect)
			{
				return $this->redirectToRoute('order');
			}
		}
		$entityManager->refresh($user);

		return $this->render('Profile/Settings/profile_settings.html.twig',
		                     [
			                     'user' => $user,
			                     'form' => $form->createView()
		                     ]
		);
	}
	//</editor-fold>

	//<editor-fold desc="Change Password">
	/**
	 * @Route("/changepassword", name="user_changepassword")
	 * @param Request                      $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @return Response
	 * @throws \LogicException
	 */
	public function changePasswordTabAction(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
	{
		$user = $this->getUser();
		$changePasswordModel = new ChangePassword();
		$form = $this->createForm(ChangePasswordType::class, $changePasswordModel);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$password = $passwordEncoder->encodePassword($this->getUser(), $changePasswordModel->getNewPassword());
			$user->setPassword($password);
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();
			$this->addFlash('notice', 'Password changed[PH]');
		}

		return $this->render('Profile/ChangePassword/profile_changepassword.html.twig',
		                     [
			                     'user' => $this->getUser(),
			                     'form' => $form->createView()
		                     ]
		);
	}
	//</editor-fold>
}