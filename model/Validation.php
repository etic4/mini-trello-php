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

    public static function is_unique_email($user): bool {
        return is_null($user->get_by_email());
    }

    public static function is_same_password($passw1, $passw2) {
        return strcmp($passw1, $passw2) == 0;
    }

    public static function is_unique_title($board): bool {
        return $board->get_by_title() == 0;
    }

}