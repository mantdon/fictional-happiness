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
     * @param $date string
     * @return array containing available registration times at specified day.
     */
    public function fetchDay($date)
    {
        $orders = $this->em->getRepository('App:Order')->findAllOnDate($date);

        return $this->formAvailableVisitTimes($date, $orders);
    }

    /**
     * Note: Orders array MUST BE ordered by visitDate in ascending order.
     * @param $orders
     * @return array
     */
    private function formAvailableVisitTimes($date, $orders)
    {
        $availableTimes = [];
        $startTime = $this->getStartTime($date);
        $orderIndex = $this->findNextIndexAfterStartTime($orders, $startTime);

        for ($i = $startTime; $i < $this->workDayEndsAt; $i += $this->hoursInterval)
        {
            $formattedTime = $this->floatToTime($i);

            if($this->shouldTimeBeAdded($orders, $orderIndex, $formattedTime))
                $availableTimes[] = $formattedTime;
            else
                $orderIndex++;
        }

        return $availableTimes;
    }

    /**
     * Is used for skipping orders that were registered for a time out of working hours range.
     * @param $orders array
     * @param $startTime float
     * @return int
     */
    private function findNextIndexAfterStartTime($orders, $startTime)
    {
        $index = 0;

        foreach($orders as $order)
        {
            if($order->getVisitDate()->format('H:i') < $this->floatToTime($startTime))
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


    /**
     * @param $date string
     * @return float|int
     */
    private function getStartTime($date)
    {
        if($this->isToday($date)) {
            $now = $this->timeToFloat(date('H:i', time()));
            $registrationTimesPassed = ($now - $this->workDayBeginsAt) / $this->hoursInterval;
            if (abs($registrationTimesPassed - round($registrationTimesPassed)) < 0.0001)
                $registrationTimesPassed += 1;

            $offset = ceil($registrationTimesPassed) * $this->hoursInterval;
            if($offset > 0)
                return $this->workDayBeginsAt + $offset;
        }

        return $this->workDayBeginsAt;
    }

    /**
     * Checks if given date is today.
     * @param $date string
     * @return bool
     */
    private function isToday($date)
    {
        if(strcmp(date('Y-m-d', time()), $date) === 0)
            return true;
        return false;
    }

    /**
     * Converts time string to float. Hours and minutes only (H:i).
     * @param $time
     * @return float|int
     */
    private function timeToFloat($time)
    {
        $parts = explode(':', $time);
        return $parts[0] + floor(($parts[1]/60)*100) / 100;
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
     * Checks if day has any available times.
     * This shouldn't be in this class, but I have no idea where to put it and it make ease to test UnavailableDaysFinder.
     * @param $date string 'Y-m-d'
     * @return bool
     */
    public function isDayUnavailable($date)
    {
        if (count($this->fetchDay($date)) === 0)
            return true;
        return false;
    }
}