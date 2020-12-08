<?php


abstract class Validator {
    private $errors;

    public function __construct() {
        $errors = array();
    }

    protected function str_longer_than($str, $len): bool {
        return is_string($str) && strlen($str) > $len;
    }

    protected function contains_capitals($str): bool {
        return preg_match('/[A-Z]]/', $str) > 0;
    }

    protected function contains_digits($str): bool {
        return preg_match('/\d/', $str) > 0;
    }

    protected function contains_non_alpha($str): bool {
        return preg_match('/[^A-Za-z0-9 ]/', $str) > 0;
    }

    protected function valid_email($str): bool {
        return filter_var($str, FILTER_VALIDATE_EMAIL) == $str;
    }

    protected function add_error($errMsg) {
        array_push($this->errors, $errMsg);
    }

    protected function get_errors(): array {
        return $this->errors;
    }

    public abstract function validate();

}