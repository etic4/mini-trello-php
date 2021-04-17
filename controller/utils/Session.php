<?php

require_once "autoload.php";

class Session {
    public static function set_error(DisplayableError $error) {
        $_SESSION["error"] = $error;
    }

    public static function get_error(): DisplayableError {
        $error = new DisplayableError();

        if (isset($_SESSION["error"])) {
            $error = $_SESSION["error"];
            $_SESSION["error"] = new DisplayableError();
        }

        return $error;
    }

    public static function get_user() {
        return $_SESSION["user"] ?? false;
    }

    public static function resubmit(array $submit) {
        $_SESSION["resubmit"] = [];

        foreach ($submit as $key => $arg) {
            $_SESSION["resubmit"][$key] = $arg;
        }
    }

    public static function has_resubmit() {
        return !empty($_SESSION["resubmit"]);
    }

    public static function get_resubmit(string $key) {
        if (isset($_SESSION["resubmit"][$key])) {
            return $_SESSION["resubmit"][$key];
        }
        return "";
    }

    public static function empty_resubmit() {
        $_SESSION["resubmit"] = [];
    }

}