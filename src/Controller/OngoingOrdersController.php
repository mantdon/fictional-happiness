<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProgressLine;
use App\Services\MessageManager;
use App\Services\OrderCreator;
use App\Services\RouteNameAppender;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/ongoingorder")
 */
class OngoingOrdersController extends Controller
{
    /**
     * @Route("/terminate/{id}", name="ongoing_order_terminate", requirements={"id"="\d+"})
     * @param Order          $order
     * @param OrderCreator   $oc
     * @param MessageManager $mm
     * @param RouteNameAppender $nameAppender
     * @return RedirectResponse
     * @throws \LogicException
     */
    public function orderTerminateAction(Order $order,
                                         OrderCreator $oc,
                                         MessageManager $mm,
                                         RouteNameAppender $nameAppender): RedirectResponse
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

        $route = $nameAppender->appendRoleToBeginning($this->getUser(), 'ongoing_orders');
        return $this->redirectToRoute($route);
    }

    /**
     * @Route("/approve/{id}", name="ongoing_order_approve", requirements={"id"="\d+"})
     * @param Order          $order
     * @param OrderCreator   $oc
     * @param MessageManager $mm
     * @param RouteNameAppender $nameAppender
     * @return RedirectResponse
     * @throws \LogicException
     */
    public function orderApproveAction(Order $order,
                                       OrderCreator $oc,
                                       MessageManager $mm,
                                       RouteNameAppender $nameAppender): RedirectResponse
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

        $route = $nameAppender->appendRoleToBeginning($this->getUser(), 'ongoing_orders');
        return $this->redirectToRoute($route);
    }

    /**
     * @Route("/completeservice/{id}", name="ongoing_order_complete_service", requirements={"id": "\d+"})
     * @param OrderProgressLine $orderProgressLine
     * @param OrderCreator      $oc
     * @param RouteNameAppender $nameAppender
     * @return RedirectResponse
     */
    public function orderServiceCompleteAction(OrderProgressLine $orderProgressLine,
                                               OrderCreator $oc,
                                               RouteNameAppender $nameAppender): RedirectResponse
    {
        $oc->completeLine($orderProgressLine);
        $route = $nameAppender->appendRoleToBeginning($this->getUser(), 'ongoing_order_show');
        return $this->redirectToRoute($route,
            [
                'id' => $orderProgressLine->getProgress()->getOrder()->getId()
            ]
        );
    }

    /**
     * @Route("/undoservice/{id}", name="ongoing_order_undo_service", requirements={"id": "\d+"})
     * @param OrderProgressLine $orderProgressLine
     * @param OrderCreator      $oc
     * @param RouteNameAppender $nameAppender
     * @return RedirectResponse
     */
    public function orderServiceUndoAction(OrderProgressLine $orderProgressLine,
                                           OrderCreator $oc,
                                           RouteNameAppender $nameAppender): RedirectResponse
    {
        $oc->undoLine($orderProgressLine);
        $route = $nameAppender->appendRoleToBeginning($this->getUser(), 'ongoing_order_show');
        return $this->redirectToRoute($route,
            [
                'id' => $orderProgressLine->getProgress()->getOrder()->getId()
            ]
        );
    }

    /**
     * @Route("/finalize/{id}", name="ongoing_order_finalize", requirements={"id": "\d+"})
     * @param Order          $order
     * @param MessageManager $mm
     * @param OrderCreator   $oc
     * @param RouteNameAppender $nameAppender
     * @return RedirectResponse
     */
    public function finalizeOrderAction(Order $order,
                                        MessageManager $mm,
                                        OrderCreator $oc,
                                        RouteNameAppender $nameAppender): RedirectResponse
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

        $route = $nameAppender->appendRoleToBeginning($this->getUser(), 'ongoing_orders');
        return $this->redirectToRoute($route);
    }

    /**
     * @Route("/view/{id}", name="ongoing_order_show", requirements={"id"="\d+"})
     * @param int $id
     * @param RouteNameAppender $nameAppender
     * @return RedirectResponse
     */
    public function ongoingOrdersShowAction($id, RouteNameAppender $nameAppender): RedirectResponse
    {
        $route = $nameAppender->appendRoleToBeginning($this->getUser(), 'ongoing_order_show');
        return $this->redirectToRoute($route,
            [
                'id' => $id
            ]
        );
    }

    /**
     * @Route("/watch/{id}", name="ongoing_order_watch", requirements={"id"="\d+"})
     * @param                   $order
     * @param RouteNameAppender $nameAppender
     * @return RedirectResponse
     */
    public function ongoingOrderWatchAction(Order $order, RouteNameAppender $nameAppender): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        $order->addWatchingUser($this->getUser());
        $em->persist($order);
        $em->flush();
        $route = $nameAppender->appendRoleToBeginning($this->getUser(), 'ongoing_order_show');
        return $this->redirectToRoute($route, ['id' => $order->getId()]);
    }

    /**
     * @Route("/unwatch/{id}", name="ongoing_order_unwatch", requirements={"id"="\d+"})
     * @param                   $order
     * @param RouteNameAppender $nameAppender
     * @return RedirectResponse
     */
    public function ongoingOrderUnwatchAction(Order $order, RouteNameAppender $nameAppender): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        $order->removeWatchingUser($this->getUser());
        $em->persist($order);
        $em->flush();
        $route = $nameAppender->appendRoleToBeginning($this->getUser(), 'ongoing_order_show');
        return $this->redirectToRoute($route, ['id' => $order->getId()]);
    }
}