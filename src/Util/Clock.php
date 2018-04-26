<?php

namespace App\Util;

class Clock
{

    /**
     * Use only this function whenever you need to get current date and time.
     * This method may use mocked time() function.
     * @return bool|\DateTime
     */
    public static function now()
    {
        $date = \DateTime::createFromFormat('U', time());
        $date->setTimezone(new \DateTimeZone('Europe/Vilnius'));
        return $date;
    }
}