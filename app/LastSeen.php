<?php

namespace App;

class LastSeen
{
    public static function ShowLastSeen($data)
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
                $minute = ' minute ';
            } else {
                $minute = ' minutes ';
            }
            if ($hours == 1) {
                $hour = ' hour ';
            } else {
                $hour = ' hours ';
            }
            if ($days == 1) {
                $day = ' day ';
            } else {
                $day = ' days ';
            }
            if ($days > 0) {
                $seen = $days . $day . $hours . $hour . $minutes . $minute . 'ago';
            } else {
                $seen = $hours . $hour . $minutes . $minute . 'ago';
            }
        } elseif ($seen == 0) {
            $seen = 'online';
        } else {
            if ($seen == 1) {
                $minute = ' minute ';
            } else {
                $minute = ' minutes ';
            }
            $seen = $seen . $minute . 'ago';
        }
        return $seen;
    }
}
