<?php

class Post extends GetPost{

    protected static function set_super_global() {
        if (is_null(self::$GoP)) {
            self::$GoP = $_POST;
        }
    }

}