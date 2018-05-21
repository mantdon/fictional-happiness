<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\User;
use App\Services\MessageManager;
use App\Services\OrderCreator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class OrderProgress extends Fixture implements DependentFixtureInterface
{
    private $orderCreator;
    private $templating;
    private $messageManager;

    public function __construct(OrderCreator $orderCreator, \Twig_Environment $templating, MessageManager $messageManager)
    {
        $this->orderCreator = $orderCreator;
        $this->templating = $templating;
        $this->messageManager = $messageManager;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 200; $i++) {
            $order = $this->getReference('order' . $i);
            $this->setOrderCompletion($order);
        }

        $manager->flush();
    }

    private function setOrderCompletion(Order $order)
    {
        $progress = $order->getProgress();
        $lines = $progress->getLines();
        $visitDate = $order->getVisitDate();

        if ($visitDate < new \DateTime('-1 week')) {
            foreach ($lines as $line) {
                $this->orderCreator->completeLine($line);
            }
        }
        else if ($visitDate < new \DateTime()) {
            foreach ($lines as $line) {
                if (random_int(1, 3) > 1) {
                    $this->orderCreator->completeLine($line);
                }
            }
            $this->orderCreator->approveOrder($order);
            $this->sendOrderApprovalMessage($order);
        }
        $this->orderCreator->finalizeOrder($order);
        if ($order->getProgress()->getIsDone()) {
            $this->sendOrderCompletionMessage($order);
        }
    }

    private function sendOrderApprovalMessage(Order $order)
    {
        $messageTitle = "Užsakymo vykdymas pradėtas";
        $messageBody = $this->templating->render('Email/order_approved.html.twig',
            [
                'order' => $order
            ]
        );

        $this->sendMessage($messageTitle, $messageBody, $order->getUser());
    }

    private function sendMessage(string $messageTitle, $messageBody, User $user)
    {
        $message = $this->messageManager->fetchOrCreateMessage($messageTitle, $messageBody);
        $this->messageManager->sendMessageToProfile($message, $user);
    }

    private function sendOrderCompletionMessage(Order $order)
    {
        $messageTitle = "Užsakymas įvykdytas";
        $messageBody = $this->templating->render('Email/order_complete.html.twig',
            [
                'order' => $order
            ]
        );

        $this->sendMessage($messageTitle, $messageBody, $order->getUser());
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            ServiceFixtures::class
        );
    }
}
