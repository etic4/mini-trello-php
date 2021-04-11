<?php

class Post extends GetPost{

    protected static function set_super_global() {
        self::$GoP = $_POST;
    }
}