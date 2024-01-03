<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymongoFormatter
{
    static function convertNumber($value, $addDecimals = true) {
        if ($addDecimals) {
            // Convert to a string with two decimal places
            $result = number_format($value, 2, '.', '');
            $result = str_replace('.', '', $result);
        } else {
            // Remove decimals
            $result = str_replace('.', '', $value);
        }
        return intval($result);
    }    

    static function reverse_parse_str($query, &$output) {
        $pairs = explode('&', $query);
        
        foreach ($pairs as $pair) {
            list($key, $value) = explode('=', $pair, 2);
            $output[urldecode($key)] = urldecode($value);
        }
    }
}
