<?php

if (!function_exists('fancyCount')) {
    /**
     * Fancifully converts numbers to a more human friendly format. e.g 1000 = 1K
     * and 1000000 = 1M.
     *
     * @param float $number
     *
     * @return string
     */
    function fancyCount($number)
    {
        $sizes = ['K', 'M', 'B', 'Z'];
        if ($number < 1000) {
            return $number;
        }
        $i = intval(floor(log($number) / log(1000)));

        return round($number / pow(1000, $i), 2).$sizes[$i - 1];
    }
}

if (!function_exists('fancyMaxCount')) {
    /**
     * Allows count only to max value.
     *
     * @param number $number
     * @param number $max
     *
     * @return string
     */
    function fancyMaxCount($number, $max = 99)
    {
        return $number > 99 ? "$max+" : "$number";
    }
}

if (!function_exists('formatNumber')) {
    /**
     * Format number
     * @param number $number number to format
     * @param number $dp decimal points
     *
     * @return string
     */
    function formatNumber($number, $dp = 0)
    {
        return number_format(floatval($number), $dp, '.', ',');
    }
}

if (!function_exists('toMoney')) {
    /**
     * Format money
     * @param number $number number to format
     * @param number $dp decimal points
     *
     * @return string
     */
    function toMoney($number, $sign = '$', $dp = 2)
    {
        return $sign.formatNumber($number, $dp);
    }
}

if (!function_exists('percentage')) {
    /**
     * Get percentage of a number
     *
     * @param number $number number to compute percentage
     * @param number $p percentage
     *
     * @return number
     */
    function percentage($number, $p = 100)
    {
        return $number * $p / 100;
    }
}
