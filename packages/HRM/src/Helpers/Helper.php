<?php

if (!function_exists('years')) {
    function years()
    {
        $array = [];
        for ($y = date('Y') + 1; $y > 2020; $y--) {
            $array[] = $y;
        }
        return $array;
    }
}

if (!function_exists('months')) {
    function months($key = null)
    {
        $array = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];

        if ($key) {
            return $array[$key];
        }
        return $array;
    }
}

if (!function_exists('weeks')) {
    function weeks()
    {
        $array = [
            1 => "Sat",
            2 => "Sun",
            3 => "Mon",
            4 => "Tue",
            5 => "Wed",
            6 => "Thu",
            7 => "Fri"
        ];

        return $array;
    }
}

if (!function_exists('monthFormat')) {
    function monthFormat($date)
    {
        if ($date != null) {
            return date('M Y', strtotime($date));
        }
    }
}

if (!function_exists('monthDays')) {
    function monthDays($year, $month)
    {
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }
}

if (!function_exists('previousMonthDays')) {
    function previousMonthDays($year, $month)
    {
        $previousMonth = date("Y-m", strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $previousMonthArr = explode('-', $previousMonth);
        return cal_days_in_month(CAL_GREGORIAN, $previousMonthArr[1], $previousMonthArr[0]);
    }
}

if (!function_exists('timeDiff')) {
    function timeDiff($time1, $time2)
    {
        $time1 = new DateTime($time1);
        $time2 = new DateTime($time2);
        if ($time1 < $time2) {
            $interval = date_diff($time2, $time1);
            return $interval->format('%h:%i');
        } else {
            return '0:00';
        }
    }
}
