<?php


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

    public static function is_unique_email(string $email): bool {
        return is_null(User::get_by_email($email));
    }

    public static function is_same_password(string $passw1, string $passw2): bool {
        return strcmp($passw1, $passw2) == 0;
    }

    public static function is_unique_title(string $title): bool {
        return is_null(Board::get_by_title($title));
    }

}