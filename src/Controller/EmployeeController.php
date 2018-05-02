<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\ChangePassword;
use App\Entity\OrderProgressLine;
use App\Form\ChangePasswordType;
use App\Form\EditUserType;
use App\Services\MessageManager;
use App\Services\OrderCreator;
use App\Services\PaginationHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
/**
 * @Route("/employee")
 */
class EmployeeController extends Controller
{
    private $pageParameterName = 'page';

	/**
	 * @Route("/", name="employee_home")
	 * @throws \LogicException
	 */
    public function home()
    {
        $user = $this->getUser();
        return $this->render( 'Employee/Home/employee_home.html.twig', array(
            'user' => $user
        ) );
    }

    /**
     * @Route ("/users/{page}", name="employee_users", defaults={"page"=1}, requirements={"page"="\d+"})
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

        return $this->render( 'Employee/Users/list.html.twig',
            [
                'users' => $paginationHandler->getResult(),
                'pageCount' => $paginationHandler->getPageCount(),
                'userCount' => $paginationHandler->getResult()->getTotalCount(),
                'currentPage' => $paginationHandler->getCurrentPage(),
                'pageParameterName' => $this->pageParameterName,
                'route' => 'employee_users'
            ]
        );
    }

    /**
     * @Route("/ongoingorders/{page}", name="employee_ongoing_orders", defaults={"page"=1}, requirements={"page"="\d+"})
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

        return $this->render( 'Employee/OngoingOrders/employee_ongoing_orders.html.twig',
            [
                'orders' => $paginationHandler->getResult(),
                'pageCount' => $paginationHandler->getPageCount(),
                'currentPage' => $paginationHandler->getCurrentPage(),
                'pageParameterName' => $this->pageParameterName,
                'route' => 'employee_ongoing_orders'
            ]
        );
    }

    /**
     * @Route("/ongoingorders/view/{id}", name="employee_ongoing_order_show", requirements={"id"="\d+"})
     * @param Order $order
     * @return Response
     */
    public function ongoingOrdersShowAction(Order $order): Response
    {
        return $this->render('Employee/OngoingOrders/employee_ongoing_order_show.html.twig',
            [
                'order' => $order
            ]
        );
    }

    /**
     * @Route("/ongoingorders/terminate/{id}", name="employee_order_terminate", requirements={"id"="\d+"})
     * @param Order          $order
     * @param OrderCreator   $oc
     * @param MessageManager $mm
     * @return RedirectResponse
     * @throws \LogicException
     */
    public function employeeOrderTerminateAction(Order $order, OrderCreator $oc, MessageManager $mm): RedirectResponse
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

        return $this->redirectToRoute('employee_ongoing_orders');
    }

    /**
     * @Route("/ongoingorders/approve/{id}", name="employee_order_approve", requirements={"id"="\d+"})
     * @param Order          $order
     * @param OrderCreator   $oc
     * @param MessageManager $mm
     * @return RedirectResponse
     * @throws \LogicException
     */
    public function employeeOrderApproveAction(Order $order, OrderCreator $oc, MessageManager $mm): RedirectResponse
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

        return $this->redirectToRoute('employee_ongoing_orders');
    }

    /**
     * @Route("/ongoingorder/completeservice/{id}", name="employee_ongoing_order_complete_service", requirements={"id": "\d+"})
     * @param OrderProgressLine $orderProgressLine
     * @param OrderCreator      $oc
     * @return RedirectResponse
     */
    public function orderServiceCompleteAction(OrderProgressLine $orderProgressLine, OrderCreator $oc): RedirectResponse
    {
        $oc->completeLine($orderProgressLine);
        return $this->redirectToRoute('employee_ongoing_order_show',
            [
                'id' => $orderProgressLine->getProgress()->getOrder()->getId()
            ]
        );
    }

    /**
     * @Route("/ongoingorder/undoservice/{id}", name="employee_ongoing_order_undo_service", requirements={"id": "\d+"})
     * @param OrderProgressLine $orderProgressLine
     * @param OrderCreator      $oc
     * @return RedirectResponse
     */
    public function orderServiceUndoAction(OrderProgressLine $orderProgressLine, OrderCreator $oc): RedirectResponse
    {
        $oc->undoLine($orderProgressLine);
        return $this->redirectToRoute('employee_ongoing_order_show',
            [
                'id' => $orderProgressLine->getProgress()->getOrder()->getId()
            ]
        );
    }

    /**
     * @Route("/ongoingorder/finalize/{id}", name="employee_ongoing_order_finalize", requirements={"id": "\d+"})
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

        return $this->redirectToRoute('employee_ongoing_orders');
    }

    /**
     * @Route("/settings/{redirect}", name="employee_settings")
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

        return $this->render('Employee/Settings/profile_settings.html.twig',
            [
                'user' => $user,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/changepassword", name="employee_changepassword")
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

        return $this->render('Employee/ChangePassword/employee_change_password.html.twig', array(
            'user' => $this->getUser(),
            'form' => $form->createView()
        ));
    }
}
