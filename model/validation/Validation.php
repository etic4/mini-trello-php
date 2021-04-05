<?php

require_once "autoload.php";

abstract class Validation {
    protected array $errors = [];

    public function get_errors(): array {
        return $this->errors;
    }
    
    public static function str_lower_than(string $str, int $len): bool {
        return is_string($str) && mb_strlen($str, "utf-8") < $len;
    }

    public static function doesnt_contains_capitals(string $str): bool {
        return preg_match('/[A-Z]/', $str) === 0;
    }

    public static function doesnt_contains_digits(string $str): bool {
        return preg_match('/\d/', $str) === 0;
    }

    public static function doesnt_contains_non_alpha(string $str): bool {
        return preg_match('/[^A-Za-z0-9 ]/', $str) === 0;
    }

    public static function valid_email(string $str): bool {
        return filter_var($str, FILTER_VALIDATE_EMAIL) == $str;
    }

    public static function strings_not_equals(string $str1, string $str2): bool {
        return $str1 != $str2;
    }

    // vrai si date 1 jour avant aujourd'hui
    public static function date_before(?Datetime $date, DateTime $dateNow) {
        return !is_null($date) && $dateNow->diff($date)->format("%r%a") < 0;
    }
}