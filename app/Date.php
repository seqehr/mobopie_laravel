<?php

namespace App;

class Date
{
    public static function ShowDate($data)
    {

        $seen = floor((time() - $data) / 60);

        $more = false;
        if ($seen > 60) {
            $more = true;
            $hours = floor($seen / 60);
            $minutes = $seen - ($hours * 60);
            if (($seen > 24) && ($more == true)) {
                $days = floor(($seen / 60) / 24);
                $hours = floor($seen / 60) - ($days * 24);
            }
            if ($minutes == 1) {
                $minute = ' m ';
            } else {
                $minute = ' m ';
            }
            if ($hours == 1) {
                $hour = ' h ';
            } else {
                $hour = ' h ';
            }
            if ($days == 1) {
                $day = ' d ';
            } else {
                $day = ' d ';
            }
            if ($days > 0) {
                $seen = $days . $day . $hours . $hour . $minutes . $minute;
            } else {
                $seen = $hours . $hour . $minutes . $minute;
            }
        } elseif ($seen == 0) {
            $seen = 'now';
        } else {
            if ($seen == 1) {
                $minute = ' m ';
            } else {
                $minute = ' m ';
            }
            $seen = $seen . $minute;
        }
        return $seen;
    }
}
