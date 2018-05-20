<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Order;
use App\Form\ReplyType;
use App\Services\UserManager;
use App\Entity\OrderProgressLine;
use App\Entity\Service;
use App\Entity\User;
use App\Form\ServiceType;
use App\Services\MessageManager;
use App\Services\OrderCreator;
use App\Services\PaginationHandler;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
	private $pageParameterName = 'page';

    /**
	 * @Route("", name="admin_home")
	 */
	public function home()
	{
		return $this->render( 'Admin/Home/admin_home.html.twig' );
	}

	//<editor-fold desc="Users">
	/**
	 * @Route ("/users/{page}", name="admin_users", defaults={"page"=1}, requirements={"page"="\d+"})
	 * @param PaginationHandler $paginationHandler
	 * @param                   $page
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 * @throws \BadMethodCallException
	 * @throws \InvalidArgumentException
	 */
	public function usersPageAction(PaginationHandler $paginationHandler, $page): Response
	{
		$paginationHandler->setQuery('App:User', 'getAll')
						  ->setPage($page)
						  ->setItemLimit(5)
						  ->addLastUsedPageUseCase('/users/ban')
						  ->addLastUsedPageUseCase('/users/unban')
						  ->paginate();

		return $this->render( 'Admin/Users/list.html.twig',
		                      [
								'users' => $paginationHandler->getResult(),
								'pageCount' => $paginationHandler->getPageCount(),
								'userCount' => $paginationHandler->getResult()->getTotalCount(),
								'currentPage' => $paginationHandler->getCurrentPage(),
								'pageParameterName' => $this->pageParameterName,
								'route' => 'admin_users'
		                      ]
		);
	}

	/**
	 * @Route("/users/ban/{id}", name="user_ban", methods="GET")
	 * @param User $user
	 * @return Response
	 */
	public function userBanAction(User $user): Response
	{
		return $this->render('Admin/Users/admin_users_ban_confirm.html.twig', ['user' => $user]);
	}

	/**
	 * @Route("/users/ban/{id}", name="user_ban_confirm", methods="POST")
	 * @param Request $request
	 * @param User    $user
	 * @return RedirectResponse
	 * @throws \LogicException
	 */
	public function userBanConfirm(Request $request, User $user): RedirectResponse
	{
		if ($this->isCsrfTokenValid('ban'.$user->getId(), $request->request->get('_token'))) {
			$user->setIsEnabled(false);
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
			$this->addFlash('notice', 'User banned[PH]');
		}
		return $this->redirectToRoute('admin_users');
	}

	/**
	 * @Route("/users/unban/{id}", name="user_unban", methods="GET")
	 * @param User $user
	 * @return Response
	 */
	public function userUnbanAction(User $user): Response
	{
		return $this->render('Admin/Users/admin_users_unban_confirm.html.twig', ['user' => $user]);
	}

	/**
	 * @Route("/users/unban/{id}", name="user_unban_confirm", methods="POST")
	 * @param Request $request
	 * @param User    $user
	 * @return RedirectResponse
	 * @throws \LogicException
	 */
	public function userUnbanConfirm(Request $request, User $user): RedirectResponse
	{
		if($this->isCsrfTokenValid('unban' . $user->getId(), $request->request->get('_token'))){
			$user->setIsEnabled(true);
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
			$this->addFlash('notice', 'User unbanned[PH]');
		}
		return $this->redirectToRoute('admin_users');
	}
	//</editor-fold>

	//<editor-fold desc="Services">
	/**
	 * @Route ("/services/{page}", defaults={"page": 1},name="admin_services", requirements={"page"="\d+"})
	 * @param PaginationHandler $paginationHandler
	 * @param                   $page
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \InvalidArgumentException
	 * @throws \BadMethodCallException
	 */
	public function servicePageAction(PaginationHandler $paginationHandler, $page): Response
	{
		$paginationHandler->setQuery('App:Service', 'getAll')
						  ->setPage($page)
						  ->setItemLimit(3)
						  ->addLastUsedPageUseCase('services/edit')
						  ->addLastUsedPageUseCase('services/delete')
						  ->paginate();

		return $this->render('Admin/Services/admin_services.html.twig',
		                     [ 'services' => $paginationHandler->getResult() ,
		                       'pageCount' =>$paginationHandler->getPageCount(),
		                       'currentPage' => $paginationHandler->getCurrentPage(),
		                       'pageParameterName' => $this->pageParameterName,
		                       'route' => 'admin_services' ]);
	}

	/**
	 * @Route("/services/new", name="service_new", methods="GET|POST")
	 * @param Request $request
	 * @return Response
	 * @throws \LogicException
	 */
	public function serviceNewAction(Request $request): Response
	{
		$service = new Service();
		$form = $this->createForm(ServiceType::class, $service);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->persist($service);
			$em->flush();
			$this->addFlash('notice', 'Service added[PH]');

			return $this->redirectToRoute('admin_services');
		}

		return $this->render('Admin/Services/admin_services_new.html.twig',
		                     [
								'service' => $service,
								'form' => $form->createView()
		                     ]
		);
	}

	/**
	 * @Route("/services/edit/{id}", name="service_edit", methods="GET|POST")
	 * @param Request $request
	 * @param Service $service
	 * @return Response
	 * @throws \LogicException
	 */
	public function serviceEditAction(Request $request, Service $service): Response
	{
		$form = $this->createForm(ServiceType::class, $service);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$this->getDoctrine()->getManager()->flush();
			$this->addFlash('notice', 'Service updated[PH]');
			return $this->redirectToRoute('admin_services');
		}

		return $this->render('Admin/Services/admin_services_edit.html.twig',
		                     [
								'service' => $service,
								'form' => $form->createView()
		                     ]
		);
	}

	/**
	 * @Route("/services/delete/{id}", name="service_show", methods="GET")
	 * @param Service $service
	 * @return Response
	 */
	public function serviceDeleteConfirmAction(Service $service): Response
	{
		return $this->render('Admin/services/admin_services_delete_confirm.html.twig', ['service' => $service]);
	}

	/**
	 * @Route("/services/delete/{id}", name="service_delete", methods="DELETE")
	 * @param Request $request
	 * @param Service $service
	 * @return Response
	 * @throws \LogicException
	 */
	public function serviceDeleteAction(Request $request, Service $service): Response
	{
		if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($service);
			$em->flush();
			$this->addFlash('notice', 'Service removed[PH]');
		}

		return $this->redirectToRoute('admin_services');
	}
	//</editor-fold>

	//<editor-fold desc="Ongoing Orders">
	/**
	 * @Route("/ongoingorders/{page}", name="admin_ongoing_orders", defaults={"page"=1}, requirements={"page"="\d+"})
	 * @param PaginationHandler $paginationHandler
	 * @param                   $page
	 * @return RedirectResponse|Response
	 * @throws \InvalidArgumentException
	 * @throws \BadMethodCallException
	 */
	public function ongoingOrdersPageAction(PaginationHandler $paginationHandler, $page): Response
	{
		$paginationHandler->setQuery('App:Order','getValidOrdersForAdmin')
						  ->setPage($page)
						  ->setItemLimit(4)
						  ->addLastUsedPageUseCase('/ongoingorders/view')
						  ->addLastUsedPageUseCase('/ongoingorders/finalize')
						  ->paginate();

		return $this->render( 'Admin/OngoingOrders/admin_ongoing_orders.html.twig',
		                      [
								'orders' => $paginationHandler->getResult(),
								'pageCount' => $paginationHandler->getPageCount(),
								'currentPage' => $paginationHandler->getCurrentPage(),
								'pageParameterName' => $this->pageParameterName,
								'route' => 'admin_ongoing_orders'
		                      ]
		);
	}

	/**
	 * @Route("/ongoingorders/view/{id}", name="admin_ongoing_order_show", requirements={"id"="\d+"})
	 * @param Order $order
	 * @return Response
	 */
	public function ongoingOrdersShowAction(Order $order): Response
	{
		return $this->render('Admin/OngoingOrders/admin_ongoing_order_show.html.twig',
		                     [
		                        'order' => $order
		                     ]
		);
	}

	/**
	 * @Route("/ongoingorders/terminate/{id}", name="admin_order_terminate", requirements={"id"="\d+"})
	 * @param Order          $order
	 * @param OrderCreator   $oc
	 * @param MessageManager $mm
	 * @return RedirectResponse
	 * @throws \LogicException
	 */
	public function adminOrderTerminateAction(Order $order, OrderCreator $oc, MessageManager $mm): RedirectResponse
	{
		$statusChangeMessage = $oc->terminateOrder($order);
		$this->addFlash('notice', $statusChangeMessage);

		$messageTitle = "Užsakymas nutrauktas";
		$messageBody = $this->renderView('Email/order_terminated.html.twig',
		                                 [
			                                 'order' => $order
		                                 ]
		);

		$message = $mm->fetchOrCreateMessage($messageTitle, $messageBody);
		$mm->sendMessageToProfile($message, $order->getUser());

		return $this->redirectToRoute('admin_ongoing_orders');
	}

	/**
	 * @Route("/ongoingorders/approve/{id}", name="admin_order_approve", requirements={"id"="\d+"})
	 * @param Order          $order
	 * @param OrderCreator   $oc
	 * @param MessageManager $mm
	 * @return RedirectResponse
	 * @throws \LogicException
	 */
	public function adminOrderApproveAction(Order $order, OrderCreator $oc, MessageManager $mm): RedirectResponse
	{
		$statusChangeMessage = $oc->approveOrder($order);
		$this->addFlash('notice', $statusChangeMessage);

		$messageTitle = "Užsakymo vykdymas pradėtas";
		$messageBody = $this->renderView('Email/order_approved.html.twig',
		                                 [
			                                 'order' => $order
		                                 ]
		);

		$message = $mm->fetchOrCreateMessage($messageTitle, $messageBody);
		$mm->sendMessageToProfile($message, $order->getUser());

		return $this->redirectToRoute('admin_ongoing_orders');
	}

	/**
	 * @Route("/ongoingorder/completeservice/{id}", name="admin_ongoing_order_complete_service", requirements={"id": "\d+"})
	 * @param OrderProgressLine $orderProgressLine
	 * @param OrderCreator      $oc
	 * @return RedirectResponse
	 */
	public function orderServiceCompleteAction(OrderProgressLine $orderProgressLine, OrderCreator $oc): RedirectResponse
	{
		$oc->completeLine($orderProgressLine);
		return $this->redirectToRoute('admin_ongoing_order_show',
		                              [
			                              'id' => $orderProgressLine->getProgress()->getOrder()->getId()
		                              ]
		);
	}

	/**
	 * @Route("/ongoingorder/undoservice/{id}", name="admin_ongoing_order_undo_service", requirements={"id": "\d+"})
	 * @param OrderProgressLine $orderProgressLine
	 * @param OrderCreator      $oc
	 * @return RedirectResponse
	 */
	public function orderServiceUndoAction(OrderProgressLine $orderProgressLine, OrderCreator $oc): RedirectResponse
	{
		$oc->undoLine($orderProgressLine);
		return $this->redirectToRoute('admin_ongoing_order_show',
		                              [
			                              'id' => $orderProgressLine->getProgress()->getOrder()->getId()
		                              ]
		);
	}

	/**
	 * @Route("/ongoingorder/finalize/{id}", name="admin_ongoing_order_finalize", requirements={"id": "\d+"})
	 * @param Order          $order
	 * @param MessageManager $mm
	 * @param OrderCreator   $oc
	 * @return RedirectResponse
	 */
	public function finalizeOrderAction(Order $order, MessageManager $mm, OrderCreator $oc): RedirectResponse
	{
		$oc->finalizeOrder($order);

		$messageTitle = "Užsakymas įvykdytas";
		$messageBody = $this->renderView('Email/order_complete.html.twig',
		                                 [
			                                 'order' => $order
		                                 ]
		);

		$message = $mm->fetchOrCreateMessage($messageTitle, $messageBody);
		$mm->sendMessageToProfile($message, $order->getUser());
		$mm->sendMessageToEmail($message, $order->getUser());

		return $this->redirectToRoute('admin_ongoing_orders');
	}
	//</editor-fold>

	//<editor-fold desc="Completed Orders">
	/**
	 * @Route("/completedorders/{page}", name="admin_completed_orders", defaults={"page"=1}, requirements={"page": "\d+"})
	 * @param PaginationHandler $paginationHandler
	 * @param                   $page
	 * @return Response
	 * @throws \InvalidArgumentException
	 * @throws \BadMethodCallException
	 */
	public function completedOrdersPageAction(PaginationHandler $paginationHandler, $page): Response
	{
		$paginationHandler->setQuery('App:Order', 'getCompletedOrdersForAdmin')
						  ->setPage($page)
						  ->setItemLimit(4)
						  ->addLastUsedPageUseCase('/completedorders/view')
						  ->paginate();

		return $this->render( 'Admin/CompletedOrders/admin_completed_orders.html.twig',
		                      [
								'orders' => $paginationHandler->getResult(),
								'pageCount' => $paginationHandler->getPageCount(),
								'currentPage' => $paginationHandler->getCurrentPage(),
								'pageParameterName' => $this->pageParameterName,
								'route' => 'admin_completed_orders'
		                      ]
		);
	}

    /**
     * @Route("/createemployee", name="admin_create_employee")
     * @param userManager $userManager
     * @return Response
     */
    public function createEmployeeAction(Request $request,
                                         UserManager $userManager): Response
    {
        $this->userManager = $userManager;
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setRole('ROLE_EMPLOYEE');
            $user->setRegistrationDate(new \DateTime());
            $this->userManager->createUser($user);

            $this->addFlash(
                'notice',
                'Registration successful, welcome ' . $user->getEmailName() . '!');

            return $this->redirectToRoute( 'admin_create_employee' );
        }

        return $this->render(
            'Admin/EmployeeRegistration/create_employee.html.twig',
            array('form' => $form->createView())
        );
    }

	/**
	 * @Route("/completedorders/view/{id}", name="admin_completed_order_show", requirements={"id"="\d+"})
	 * @param Order $order
	 * @return Response
	 */
	public function completedOrdersShowAction(Order $order): Response
	{
		return $this->render('Admin/CompletedOrders/admin_completed_order_show.html.twig',
		                     [
			                     'order' => $order
		                     ]
		);
	}
	//</editor-fold>


    /**
     * @Route ("/visitorsmail/{page}", name="admin_visitors_mail", defaults={"page"=1}, requirements={"page"="\d+"})
     * @param PaginationHandler $paginationHandler
     * @param                   $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     */
    public function visitorsMailAction(PaginationHandler $paginationHandler, $page): Response
    {
        $paginationHandler->setQuery('App:Contact', 'getAll')
            ->setPage($page)
            ->setItemLimit(5)
            ->paginate();

        return $this->render('Admin/VisitorsMail/visitors_mail.html.twig',
            [
                'visitors_mail' => $paginationHandler->getResult(),
                'pageCount' => $paginationHandler->getPageCount(),
                'currentPage' => $paginationHandler->getCurrentPage(),
                'pageParameterName' => 'page'
            ]);
    }

    /**
     * @Route("/visitorsmail/delete/{id}", name="visitors_mail_delete", methods="GET")
     * @param Contact $letter
     * @return Response
     * @throws \LogicException
     */
    public function visitorsMailDelete(Contact $letter): Response
    {
        return $this->render('Admin/VisitorsMail/visitors_mail_delete.html.twig',
            array('letter' => $letter));
    }

    /**
     * @Route("/visitorsmail/delete/{id}", name="visitors_mail_delete_confirm", methods="DELETE")
     * @param Request $request
     * @param Contact    $letter
     * @return RedirectResponse
     * @throws \LogicException
     */
    public function visitorsMailDeleteConfirmAction(Request $request, Contact $letter): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$letter->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($letter);
            $em->flush();
            $this->addFlash('notice', 'Letter deleted[PH]');
        }
        return $this->redirectToRoute('admin_visitors_mail');
    }

    /**
     * @Route("/visitorsmail/reply/{id}", name="visitors_mail_reply")
     * @param Request $request
     * @param Contact $contact
     * @param \Swift_Mailer $mailer
     * @return Response
     * @throws \LogicException
     */
    public function visitorsMailReply(Request $request, Contact $contact, \Swift_Mailer $mailer): Response
    {
        $form = $this->createForm(ReplyType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $message = (new \Swift_Message($contact->getSubject()))
                ->setFrom('administracija@servisas.com')
                ->setTo($contact->getEmail())
                ->setBody(
                    $this->renderView(
                        'Admin/VisitorsMail/visitors_mail_reply_template.html.twig',
                        array('recipient_name' => $contact->getName(),
                            'sender_name' => $formData['name'],
                            'text' => $formData['comment']
                            )
                    ),
                    'text/html'
                );
            $mailer->send($message);
            $contact->setIsAnswered(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            $this->addFlash('notice', 'Form submitted[PH]');
            return $this->redirectToRoute('admin_visitors_mail');
        }
        return $this->render('Admin/VisitorsMail/visitors_mail_reply.html.twig',
            array('form' => $form->createView()));
    }

}