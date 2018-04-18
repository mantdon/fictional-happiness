<?php

namespace App\Controller;

use App\Entity\ChangePassword;
use App\Entity\EditUser;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Form\ChangePasswordType;
use App\Form\EditUserType;
use App\Form\RegistrationType;
use App\Form\VehicleType;
use App\Services\MessageManager;
use App\Services\PaginatedListFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Helpers\Pagination;
use \DateTime;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends Controller
{
	private $pageParameterName = "page";

	/**
	 * @Route("/user/orders", name="user_orders")
	 */
	public function ordersTabActions(Request $request){
		$page = $this->loadPageValue( $request );
		return $this->redirectToRoute( 'user_orders_page', [ $this->pageParameterName => $page ] );
	}

	/**
	 * @Route("/user/orders/page/{page}", name="user_orders_page", requirements={"page"="\d+"})
	 */
	public function ongoingOrdersPageAction( PaginatedListFetcher $listFetcher, $page )
	{
		$list = $listFetcher->getOrdersForUser($page , 3);
		// Load first page on invalid page entry.
		// 'totalCount' check is needed to avoid redirection loops on empty list.
		if( $list['totalCount'] !== 0 && $page > $list['pageCount'] || $page < 1 )
			return $this->redirectToRoute( 'user_orders_page', array( $this->pageParameterName => 1 ) );

		return $this->render( 'Profile/Orders/profile_orders.html.twig',
		                      array(
		                      	'user' => $this->getUser(),
		                      	    'orders' => $list['items'],
			                      'pageCount' => $list['pageCount'],
			                      'currentPage' => $page,
			                      'pageParameterName' => $this->pageParameterName,
			                      'route' => 'user_orders_page' ) );
	}

	/**
	 * @Route("/user/vehicles", name="user_vehicles")
	 */
	public function vehiclesTabAction(){
		return $this->render('Profile/Vehicles/profile_vehicles.html.twig', array(
			'user' => $this->getUser(),
			'vehicles' => $this->getUser()->getVehicles()
		));
	}

	/**
	 * @Route("/user/messages", name="user_messages")
	 */
	public function messagesTabAction(Request $request){
		$page = $this->loadPageValue($request);
		return $this->redirectToRoute('user_message_page', [$this->pageParameterName => $page]);
	}

	/**
	 * @Route("user/messages/page/{page}", name="user_message_page", requirements={"page"="\d+"})
	 */
	public function messagesPageAction(PaginatedListFetcher $listFetcher, $page){
		$list = $listFetcher->getPaginatedMessagesList($this->getUser(),'App:MessageMetaData', $page, 4);

		if($list['totalCount'] !== 0 && $page > $list['pageCount'] || $page < 1)
			return $this->redirectToRoute('user_message_page', array($this->pageParameterName => 1));

		return $this->render('Profile/Messages/profile_messages.html.twig',
		                     array('user' => $this->getUser(),
			                     'messages' => $list['items'],
			                     'pageCount' => $list['pageCount'],
			                     'messagesCount' => $list['totalCount'],
			                     'currentPage' => $page,
			                     'pageParameterName' => $this->pageParameterName,
			                     'route' => 'user_message_page'
		                     ));
	}

	/**
	 * @Route("/user/settings", name="user_settings")
	 */
	public function settingsTabAction(Request $request){
	    $user = $this->getUser();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('notice', 'User updated[PH]');
        }

		return $this->render('Profile/Settings/profile_settings.html.twig', array(
			'user' => $user,
            'form' => $form->createView()
		));
	}
	/**
	 * @Route("/user/changepassword", name="user_changepassword")
	 */
	public function changepasswordTabAction(Request $request, UserPasswordEncoderInterface $passwordEncoder){
	    $user = $this->getUser();
	    $changePasswordModel = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class, $changePasswordModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($this->getUser(), $changePasswordModel->getNewPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('notice', 'Password changed[PH]');
        }

		return $this->render('Profile/ChangePassword/profile_changepassword.html.twig', array(
			'user' => $this->getUser(),
            'form' => $form->createView()
		));
	}

	//<editor-fold desc="Message sending examples">
	/**
	 * @Route("/user/messages/send", name="send_message")
	 */
	public function messageToProfile(MessageManager $mm){
		$messageTitle = "New message";
		// Content is expected to be rendered from an html.
		$messageContent = $this->renderView('Email/order_placed.html.twig');
		// May be used when sending message to user's profile.
		$customSender = 'Extremely witty name';
		// User object should be retrieved from relevant entity.
		// Sending to yourself at the moment.
		$recipient = $this->getUser();

		$message = $mm->fetchOrCreateMessage($messageTitle, $messageContent);
		$mm->sendMessageToProfile($message, $recipient);

		return $this->redirectToRoute("user_messages");
	}

	/**
	 * @Route("/user/messages/sendemail", name="send_email_message")
	 */
	public function messageToEmail(MessageManager $mm)
	{
		$messageTitle = "New message";
		// Content is expected to be rendered from an html.
		$messageContent = $this->renderView('Email/order_placed.html.twig');
		// User object should be retrieved from relevant entity.
		// Sending to yourself at the moment.
		$recipient = $this->getUser();

		$message = $mm->fetchOrCreateMessage($messageTitle, $messageContent);
		$mm->sendMessageToEmail($message, $recipient);

		return $this->redirectToRoute("user_messages");
	}
	//</editor-fold>

	//<editor-fold desc="TO-DO: CLEAN THIS UP!">
	private function loadPageValue(Request $request, $defaultValue = 1){
		$previous_page_key = 'previous_page';
		$page = NULL;
		if($this->previousPageRequired($previous_page_key))
			$page = $this->loadAndClearPageFromSession($previous_page_key);
		if($page === NULL)
			return $defaultValue;
		return $page;
	}

	private function previousPageRequired(string $previous_page_key){
		if($this->get('session')->has($previous_page_key))
			return true;
		return false;
	}

	public function loadAndClearPageFromSession(string $page_key){
		$savedPage = $this->get( 'session' )->get($page_key);
		$this->get('session')->remove($page_key);

		return $savedPage;
	}
	//</editor-fold>
}