<?php


class Get extends GetPost {

    protected static function set_super_global() {
        self::$GoP = $_GET;
    }
}