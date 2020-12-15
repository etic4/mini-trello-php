<?php

/**/
class Validation {
    
    public static function str_longer_than($str, $len): bool {
        return is_string($str) && strlen($str) > $len;
    }

    public static function contains_capitals($str): bool {
        return preg_match('/[A-Z]]/', $str) > 0;
    }

    public static function contains_digits($str): bool {
        return preg_match('/\d/', $str) > 0;
    }

    public static function contains_non_alpha($str): bool {
        return preg_match('/[^A-Za-z0-9 ]/', $str) > 0;
    }

    public static function valid_email($str): bool {
        return filter_var($str, FILTER_VALIDATE_EMAIL) == $str;
    }

}