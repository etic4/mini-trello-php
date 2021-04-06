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
}