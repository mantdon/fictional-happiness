<?php

namespace App\Services;


use App\Entity\Order;
use App\Entity\OrderProgress;
use App\Entity\OrderProgressLine;
use App\Entity\Service;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use App\Helpers\EnumOrderStatusType;

class OrderCreator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createOrder($vehicle, $services, $date, \App\Entity\User $user)
    {
        $order = new Order();
        $visitDate = new \DateTime($date);
        $services = $this->em->getRepository('App:Service')->findBy(['id' => $this->getIds($services)]);
        $order
            ->setVehicle($this->getReference('App:Vehicle', $vehicle['id']))
            ->setServices($services)
            ->setCost($this->calculateCost($services))
            ->setVisitDate($visitDate)
	        ->setUser($user)
	        ->setStatus(EnumOrderStatusType::Placed);

        $this->setupProgress($order);

        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    private function setupProgress(\App\Entity\Order $order)
    {
    	$orderProgress = new OrderProgress();
    	$orderProgress->setNumberOfServicesCompleted(0)
	                ->setIsDone(0)
				    ->setOrder($order);

    	$order->setProgress($orderProgress);
    	$this->em->persist($orderProgress);

    	$this->setupProgressLines($order);
    }

    private function setupProgressLines(\App\Entity\Order $order)
    {
    	$orderProgress = $order->getProgress();
    	$progressLines = $orderProgress->getLines();
    	$services = $order->getServices();

    	foreach($services as $service)
	    {
	    	$orderProgressLine = new OrderProgressLine();
	    	$orderProgressLine->setProgress($orderProgress)
			                    ->setService($service)
			                    ->setIsDone(0);
	    	$progressLines->add($orderProgressLine);
	    	$this->em->persist($orderProgressLine);
	    }
    }

    public function completeLine(\App\Entity\OrderProgressLine $orderProgressLine)
    {
    	if($orderProgressLine->getIsDone() === false)
	    {
	    	$orderProgress = $orderProgressLine->getProgress();
	    	$orderProgressLine->setIsDone(true)
			                  ->setCompletedOn(new \DateTime(date('Y/m/d H:i:s')));
	    	$orderProgress->incrementNumberOfServicesCompleted();

	    	$this->em->persist($orderProgress);
	    	$this->em->persist($orderProgressLine);
	    	$this->em->flush();
	    }
    }

    public function undoLine(\App\Entity\OrderProgressLine $orderProgressLine)
    {
    	if($orderProgressLine->getIsDone() === true)
	    {
	    	$orderProgress = $orderProgressLine->getProgress();
	    	$orderProgressLine->setIsDone(false)
		                      ->setCompletedOn(NULL);
	    	$orderProgress->decrementNumberOfServicesCompleted();

		    $this->em->persist($orderProgress);
	    	$this->em->persist($orderProgressLine);
	    	$this->em->flush();
	    }
    }

    public function finalizeOrder(\App\Entity\Order $order)
    {
	    if($order->getServices()->count() === $order->getProgress()->getNumberOfServicesCompleted())
	    {
	    	$this->changeStatus($order, EnumOrderStatusType::Complete);
		    $order->getProgress()->setIsDone( true )
			                     ->setCompletionDate(new \DateTime(date('Y/m/d H:i:s')));
	    }
    }

    private function changeStatus(\App\Entity\Order $order, string $status)
    {
	    $order->setStatus($status);
	    $this->em->persist($order);
	    $this->em->flush();
    }

    public function cancelOrder(\App\Entity\Order $order)
    {
	    $message = "Order cancelled";
	    if($order->getStatus() !== EnumOrderStatusType::getValue(EnumOrderStatusType::Placed))
		    $message = "Only recently placed and not ongoing orders may be cancelled.";
	    else
	    	$this->changeStatus($order, EnumOrderStatusType::Canceled);
	    return $message;
    }

    public function terminateOrder(\App\Entity\Order $order)
    {
	    $message = "Order terminated";
	    if($order->getStatus() !== EnumOrderStatusType::getValue(EnumOrderStatusType::Placed))
		    $message = "Only recently placed and not ongoing orders may be terminated.";
	    else
	    	$this->changeStatus($order, EnumOrderStatusType::Terminated);
	    return $message;
    }

    public function approveOrder(\App\Entity\Order $order)
    {
    	$message = "Užsakymo #".$order->getId()." vykdymas pradėtas.";
	    if($order->getStatus() !== EnumOrderStatusType::getValue(EnumOrderStatusType::Placed))
	    	$message = "Tik naujai pateiktų ir vykdyti nepradėtų užsakymų vykdymas gali būti pradėtas.";
	    else
	    	$this->changeStatus($order, EnumOrderStatusType::Ongoing);
	    return $message;
    }

    /**
     * @param $entityName String
     * @param $id integer
     * @return null|object used for setting database relationships.
     */
    private function getReference($entityName, $id)
    {
        return $this->em->getReference($entityName,$id);
    }

    private function calculateCost($array)
    {
        $cost = 0;

        /**
         * @var $item Service
         */
        foreach ($array as $item)
        {
            $cost += $item->getPrice();
        }

        return $cost;
    }

    /**
     * Picks out id of every element in array.
     * @param $data array
     * @return array
     */
    private function getIds($data)
    {
        $ids = [];

        foreach ($data as $item)
        {
            $ids[] = $item['id'];
        }

        return $ids;
    }
}