<?php

namespace App\Services;


use Doctrine\ORM\EntityManagerInterface;

class AvailableTimesFetcher
{
    /*
     * For now. Will come up with something smarter.
     */
    private $workDayBeginsAt = 9;
    private $workDayEndsAt = 17;
    private $hoursInterval = 2;
    /***/

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $date
     * @return array containing available registration times at specified day.
     */
    public function fetchDay($date)
    {
        $orders = $this->em->getRepository('App:Order')->findAllOnDate($date);

        return $this->formAvailableVisitTimes($orders);
    }

    /**
     * Note: Orders array MUST BE ordered by visitDate in ascending order.
     * @param $orders
     * @return array
     */
    private function formAvailableVisitTimes($orders)
    {
        $availableTimes = [];
        $orderIndex = $this->findIndexOfFirstOrderInWorkday($orders);

        for ($i = $this->workDayBeginsAt; $i < $this->workDayEndsAt; $i += $this->hoursInterval)
        {
            $formattedTime = $this->floatToTime($i);

            if($this->shouldTimeBeAdded($orders, $orderIndex, $formattedTime))
                $availableTimes[] = $formattedTime;
            else
                $orderIndex++;
        }

        return $availableTimes;
    }


    private function shouldTimeBeAdded($orders, $orderIndex, $formattedTime)
    {
        if($orderIndex < count($orders))
        {
            $currentOrderTime = $orders[$orderIndex]->getVisitDate()->format('H:i');
            if($this->isTimeOccupied($formattedTime, $currentOrderTime))
                return false;
        }

        return true;
    }

    /**
     * Smarter "algorithm" should be used here
     * @param $orderTime |DateTime
     * @param $checkTime \DateTime
     * @return bool
     */
    private function isTimeOccupied($orderTime, $checkTime)
    {
        if (strcmp($orderTime, $checkTime) === 0)
                return true;

        return false;
    }

    /**
     * Is used for skipping orders that were registered for a time out of working hours range.
     * @param $orders
     * @return int
     */
    private function findIndexOfFirstOrderInWorkday($orders)
    {
        $index = 0;

        foreach($orders as $order)
        {
            if($order->getVisitDate()->format('H:i') < $this->floatToTime($this->workDayBeginsAt))
                $index++;
        }

        return $index;
    }

    private function floatToTime($number)
    {
        $hours = floor($number);
        $minutes = 60 * ($number % 1);

        return date('H:i', strtotime($hours . ':' . $minutes));
    }
}