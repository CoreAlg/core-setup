<?php

namespace CoreSetup\Helpers;

use Carbon;

class Parser
{
    public static function parseAmount($value)
    {
        $negative = false;

        if($value < 0){
            $negative = true;
        }

        // converting a number from negative to positive
        $number = abs($value);

        $d = 0;

        $n = number_format($number, $d, '.', '');
        $n = strrev($n);

        if ($d) {
            $d++;
        }
        $d += 3;

        if (strlen($n) > $d) {
            $n = substr($n, 0, $d) . ',' . implode(',', str_split(substr($n, $d), 2));
        }

        $formattedAmount = strrev($n);
        
        return $negative === true ? "({$formattedAmount})" : $formattedAmount;

    }

    public static function parseDate($date, $format=null)
    {
        if(is_null($format) === true){
            $format = "Y-m-d";
        }

        return (string)Carbon::parse($date)->format($format);
    }

    public static function now($format = null)
    {
        if (is_null($format) === true) {
            $format = "Y-m-d";
        }

        return (string) Carbon::now()->format($format);
    }
}