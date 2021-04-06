<?php

class Request {
    public static function is_get(): bool {
        return $_SERVER["REQUEST_METHOD"] == "GET";
    }

    public static function is_post(): bool {
        return $_SERVER["REQUEST_METHOD"] == "GET";
    }
}