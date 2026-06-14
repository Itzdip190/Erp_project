<?php

if (!function_exists('formatIndianCurrency')) {
    function formatIndianCurrency(float $amount): string
    {
        if ($amount >= 10000000) {
            return '₹' . number_format(round($amount / 10000000, 2)) . 'Cr';
        } elseif ($amount >= 100000) {
            return '₹' . number_format(round($amount / 100000, 2)) . 'L';
        } elseif ($amount >= 1000) {
            return '₹' . number_format($amount);
        }
        return '₹' . number_format($amount, 2);
    }
}

if (!function_exists('formatIndianNumber')) {
    function formatIndianNumber(int|float $number): string
    {
        return number_format($number);
    }
}
