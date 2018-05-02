<?php

namespace App\Services;


use Doctrine\ORM\EntityManagerInterface;

class UnavailableDaysFinder
{
    private $availableTimesFetcher;

    private $registrationBoundsInMonths = 3; //all dates after this interval will be unavailable.

    public function __construct(AvailableTimesFetcher $availableTimesFetcher)
    {
        $this->availableTimesFetcher = $availableTimesFetcher;
    }

    public function findDays()
    {
        $unavailableDays = [];

        $now = new \DateTime(date('Y-m-d', time()));

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
    private function findDaysInMonth($date)
    {
        $unavailableDays = [];
        $daysInMonth = $this->getDaysInMonth($date);
        $dateTemplate = $date->format('Y-m-');

        for ($day = $date->format('d'); $day <= $daysInMonth; $day++)
        {
            $dateToCheck = $dateTemplate . $this->formatDayString($day);
            if ($this->availableTimesFetcher->isDayUnavailable($dateToCheck)) {
                $unavailableDays[] = $dateToCheck;
            }
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