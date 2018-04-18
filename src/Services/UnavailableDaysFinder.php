<?php

namespace App\Services;


use Doctrine\ORM\EntityManagerInterface;

class UnavailableDaysFinder
{
    private $em;
    private $availableTimesFetcher;

    private $registrationBoundsInMonths = 3; //all dates after this interval will be unavailable.

    public function __construct(EntityManagerInterface $em,
                                AvailableTimesFetcher $availableTimesFetcher)
    {
        $this->em = $em;
        $this->availableTimesFetcher = $availableTimesFetcher;
    }

    public function findDays()
    {
        $unavailableDays = [];

        $now = new \DateTime();

        for($month = 0; $month < $this->registrationBoundsInMonths; $month++)
        {
            $unavailableDays = array_merge($unavailableDays, $this->findDaysInMonth($now));
            $now->add(new \DateInterval('P1M'));
        }

        return $unavailableDays;
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
            $dateToCheck = $dateTemplate . $this->formatDayString($day);
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

    private function formatDayString($day)
    {
        if($day < 10)
            return '0' . $day;
        return $day;
    }
}