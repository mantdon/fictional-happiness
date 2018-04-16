<?php

namespace App\Services;


use Doctrine\ORM\EntityManagerInterface;

class UnavailableDaysFinder
{
    private $em;
    private $availableTimesFetcher;

    public function __construct(EntityManagerInterface $em,
                                AvailableTimesFetcher $availableTimesFetcher)
    {
        $this->em = $em;
        $this->availableTimesFetcher = $availableTimesFetcher;
    }

    /**
     * Not the most efficient solution, but works. Should be done in one query.
     * @param $date \DateTime
     */
    public function findDaysInMonth($date)
    {
        $unavailableDays = [];
        $daysInMonth = $this->getDaysInMonth($date);
        $dateTemplate = $date->format('Y-m-');

        for($day = 1; $day <= $daysInMonth; $day++)
        {
            $dateToCheck = $dateTemplate . $day;
            $orders = $this->availableTimesFetcher->fetchDay($dateToCheck);

            if(count($orders) === 0)
                $unavailableDays[] = $dateToCheck;
        }

        return $unavailableDays;
    }

    private function getDaysInMonth($date)
    {
        return $date->format('t');
    }
}