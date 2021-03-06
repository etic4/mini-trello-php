<?php

require_once "autoload.php";

class Validation {
    protected array $errors = [];

    public function get_errors(): array {
        return $this->errors;
    }

    protected function base_validate_title($title) {
        if (self::str_lower_than($title, 3)) {
            $this->errors[] = "Title must be at least 3 characters long";
        }

        if (self::str_contains_only_spaces($title)) {
            $this->errors[] = "Title can't contains only spaces";
        }
    }


    // -- Fonctions génériques ---

    public static function str_lower_than(string $str, int $len): bool {
        return mb_strlen($str, "utf-8") < $len;
    }

    public static function str_contains_only_spaces($str): bool {
        return self::str_lower_than(str_replace(" ", "", $str), 1) ;
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

    // vrai si date avant aujourd'hui
    public static function date_before(?Datetime $date, DateTime $dateNow): bool {
        return !is_null($date) && $dateNow->diff($date)->format("%r%a") < 0;
    }

}