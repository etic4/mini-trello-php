<?php

class Post extends GetPost{

    protected static function set_super_global() {
        if (is_null(self::$GP)) {
            self::$GP = $_POST;
        }
    }

}