<?php

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

function formatNumber($number, $dp = 0)
{
    return number_format(floatval($number), $dp, '.', ',');
}

function toNaira($number, $dp = 2, $strike = false)
{
    $symbol = '₦';
    $n = $strike ? '<strike>N</strike>' : $symbol;

    return $n.formatNumber($number, $dp);
}

function toMoney($number, $sign = '$', $dp = 2)
{
    return $sign.formatNumber($number, $dp);
}

function percentage($number, $p = 100)
{
    return $number * $p / 100;
}

function fancyTime($time)
{
}
