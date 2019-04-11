<?php
/**
 * Created by PhpStorm.
 * User: Manoj Thakur
 * Date: 3/26/2019
 * Time: 8:30 AM
 */
class MFuncs {
    public static function substring($in_str, $find_str, $nocase = false) {
        $retval = false;
        $use_in_str = trim($in_str);
        $use_find_str = trim($find_str);
        if ($nocase) {
            $use_in_str = strtoupper($use_in_str);
            $use_find_str = strtoupper($use_find_str);
        }
        if (strpos($use_in_str, $use_find_str) !== false) {
            $retval = true;
        }
        return $retval;
    }

    public static function arr_to_str($arr, $delim = " ") {
        $out_string="";
        $first_pass = true;
        foreach ($arr as $elem) {
            if ($first_pass == true) {
                $out_string .= $elem;
                $first_pass = false;
            } else {
                $out_string .= ($delim . $elem);
            }
        }
        return ($out_string);
    }
}